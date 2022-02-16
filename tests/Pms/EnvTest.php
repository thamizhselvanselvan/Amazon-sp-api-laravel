<?php

namespace Tests\Pms;

use Tests\TestCase;

class EnvTest extends TestCase
{
    public function test_env_match_with_ex()
    {
        $env_examples = [];
        $env_actuals = [];

        $correct_lines = [];
        $handle = fopen(".env.example", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (substr($line, 0, 1) !== "#" && $line != '') {
                    array_push($correct_lines, $line);
                } else {
                    if (strpos($line, "=") !== false) {
                        array_push($correct_lines, str_replace("#", "", $line));
                    }
                }
            }
            fclose($handle);
        } else {
            echo "Error opening the file";
        }

        foreach ($correct_lines as $key => $value) {
            $ans = explode("=", $value);
            $env_examples[$ans[0]] = $ans[0];
        }


        /* #############################  */

        $correct_lines = [];
        $handle = fopen(".env", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (substr($line, 0, 1) !== "#" && $line != '') {
                    array_push($correct_lines, $line);
                } else {
                    if (strpos($line, "=") !== false) {
                        array_push($correct_lines, str_replace("#", "", $line));
                    }
                }
            }
            fclose($handle);
        } else {
            echo "Error opening the file";
        }

        foreach ($correct_lines as $key => $value) {
            $ans = explode("=", $value);
            $env_actuals[$ans[0]] = $ans[0];
        }

        /* #############################  */

        $varience1 = array_diff_key($env_examples, $env_actuals);
        $varience2 = array_diff_key($env_actuals, $env_examples);

        if (!$varience1 && !$varience2) {
            $this->assertTrue(true);
        } else {
            $message = '';

            if (!empty($varience1)) {
                $message .= " Missing keys in env (" . implode(",", array_keys($varience1)) . ") ";
            }
            if (!empty($varience2)) {
                $message .= " Missing keys in env.example (" . implode(",", array_keys($varience2)) . ") ";
            }
            $this->assertFalse("env mismatch! " . $message);
        }
    }
}
