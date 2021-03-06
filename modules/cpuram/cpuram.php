<?php
/*
 * http://noczone.com
 * License : http://www.mozilla.org/MPL/2.0/
 * 
 * 
 */

class module_cpuram {

    private $module_name = 'cpuram';

    function __construct() {
        
    }

    public function run() {


	foreach (config::$module[$this->module_name] as $key => $val){
		$$key = $val;
	}

        ob_start();
        passthru('/usr/bin/top -b -n 1');
        $output = ob_get_clean();
        ob_clean();

        ob_start();
        passthru('top -bn2 | grep "Cpu(s)"'); // Cpu(s):  1.5%us,  0.7%sy,  0.0%ni, 97.4%id,  0.0%wa,  0.0%hi,  0.4%si,  0.0%st
        $percentage = ob_get_clean();
        ob_clean();
        $percentage = substr($percentage,strpos($percentage,'Cpu(s):',strpos($percentage,"\n"))+7);

        $exps = explode(',',$percentage);
        $percentage = 0;
        foreach ($exps as $ex){
                if ($ex != ''){
                        if (strtolower(substr($ex,-2)) != 'id'){
                                $percentage += floatval($ex);
                        }
                }
        }

// end cpu percentage


        $output = str_replace("\t", ' ', $output);
        $output = str_replace('   ', ' ', $output);
        $output = str_replace('  ', ' ', $output);
        $output = str_replace('  ', ' ', $output);
        $output = str_replace('  ', ' ', $output);

        $head_sep = strpos($output, "\n\n");
        $header = substr($output, 0, $head_sep);
        $body = substr($output, $head_sep);

        // learn header
        $header_lines = explode("\n", $header);
        // load average load average: 0.24, 0.35, 0.40
        preg_match('/load\ average:\ ([0-9\.]*),\ ([0-9\.]*),\ ([0-9\.]*)/i', $header_lines[0], $loads);
        $out["cpu"][1] = round($loads[1] / $cores, 2);
        $out["cpu"][2] = round($loads[2] / $cores, 2);
        $out["cpu"][3] = round($loads[3] / $cores, 2);

        // learn body
        $body_lines = explode("\n", $body);
        unset($body_lines[0]);
        unset($body_lines[1]);
        unset($body_lines[2]);
        $totalCPU = 0;
        $totalRAM = 0;
        foreach ($body_lines as $line) {
            $line = str_replace("\t", ' ', $line);
            $line = str_replace("    ", ' ', $line);
            $line = str_replace("   ", ' ', $line);
            $line = str_replace("  ", ' ', $line);
            $line = str_replace("  ", ' ', $line);
            $line_exp = explode(' ', $line);
            foreach ($line_exp as $lk => $lv) {
                if (trim($lv) == '') {
                    unset($line_exp[$lk]);
                }
            }
            if (isset($line_exp[9]) && isset($line_exp[10])) {
                $app = strtolower($line_exp[count($line_exp) - 1]);
                $cpu = $line_exp[count($line_exp) - 4];
                $mem = $line_exp[count($line_exp) - 3];
                if (!in_array($app, $watch)) {
                    $app = 'other';
                }
                if (!isset($out["cpu"]["services"][$app])) {
                    $out["cpu"]["services"][$app] = 0;
                }
                if (!isset($out["ram"]["services"][$app])) {
                    $out["ram"]["services"][$app] = 0;
                }
                $out["cpu"]["services"][$app] += $cpu;
                $totalCPU+=$cpu;
                $out["ram"]["services"][$app] += $mem;
                $totalRAM += $mem;
            }
        }
        $out["cpu"]["services"]['idle'] = 100 - $totalCPU;
        $out["cpu"]['total_p'] = $totalCPU;
        $out["cpu"]['percentage'] = $percentage;


exec('free -mo', $out);
preg_match_all('/\s+([0-9]+)/', $out[1], $matches);
list($total, $used, $free, $shared, $buffers, $cached) = $matches[1];
//echo "Memory: " . $used . "/" . $total;
$usedRam = round(($used-$cached)/$total*100,2);

        $out["ram"]["total_used"] = $usedRam;
        $out["ram"]["total_free"] = 100 - $totalRAM;
        ksort($out["cpu"]["services"]);

        // get disk usage

        $df = shell_exec('df');
        $df = str_replace("\t", ' ', $df);
        $df = str_replace('   ', ' ', $df);
        $df = str_replace('  ', ' ', $df);
        $df = str_replace('  ', ' ', $df);
        $df = str_replace('  ', ' ', $df);
        $df = explode("\n", $df);

        foreach ($df as $line => $dfLine) {
            if ($line > 0) { // forget about first line
                $dfs = explode(' ', $dfLine);
                if (strtoupper($dfs[0]) != 'NONE' && trim($dfs[0]) != ''){
                    $out["disk"]['mount'][$dfs[0]] = $dfs[4];
                }
            }
        }//

        return $out;
    }

}
