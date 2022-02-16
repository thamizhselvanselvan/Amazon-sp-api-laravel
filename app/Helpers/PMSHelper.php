<?php

if (!function_exists('po')) {
    function po($obj)
    {
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
    }
}

if (!function_exists('func_flush')) {
    function func_flush($s = NULL, $display_mode = TRUE)
    {
        if (!is_null($s))
            echo $s;

        $max_length = 0;
        if (preg_match("/Apache(.*)Win/S", getenv('SERVER_SOFTWARE'))) {
            $max_length = 2500;
        } elseif (preg_match("/(.*)MSIE(.*)\)$/S", getenv('HTTP_USER_AGENT'))) {
            $max_length = 256;
        }

        if ($max_length) {

            if ($display_mode === TRUE) {
                echo str_repeat(" ", $max_length);
            } else {
                $str = '<!-- ';
                $end = ' -->';
                while (strlen($str) < $max_length) {
                    $str .= md5(rand());
                }

                $str = substr($str, 0, $max_length - strlen($end)) . $end;
                echo $str;
            }
        }

        @ob_flush();

        flush();
    }
}

if (!function_exists('msg')) {

    function msg($string = '', $delay = 20000)
    {
        echo "$string";
        if (php_sapi_name() === 'cli') {
            echo PHP_EOL;
        } else {
            echo '<BR>';
        }
        @func_flush();
        session_write_close();
        // usleep($delay);
    }
}

if (!function_exists('send_mail')) {
    function send_mail($mailContent)
    {
        if (config('app.env') == 'local') {
            $sendTo = config('pms.DEFAULT_EMAIL');
        } else {
            $sendTo  = $mailContent['email'];
        }

        if (app()->environment() !== 'local') {
            if ($mailContent['attachment'] == NULL) {
                Mail::to($sendTo)->send(new SendMail($mailContent));
            } else {
                Mail::to($sendTo)->send(new SendMailWithAttachment($mailContent));
            }
        } else {
            if ($mailContent['attachment'] == NULL) {
                Mail::to($sendTo)->send(new SendMail($mailContent));
            } else {
                Mail::to($sendTo)->send(new SendMailWithAttachment($mailContent));
            }
        }
    }
}

if (!function_exists('cacheDBQuery')) {
    function cacheDBQuery($connection, $query)
    {
        $key = md5($query);
        $value = Cache::get($key, function () use ($connection, $query) {
            return DB::connection($connection)->select($query);
        });
        return $value;
    }
}