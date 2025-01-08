<?php

    class Helpers {
        public function format_date($datatime) {
            $date = explode(' ', $datatime)[0];
    
            date_default_timezone_set('Etc/GMT-1');

            $diff = date_diff(new DateTime($datatime), new DateTime('now'));
            
            if ($diff -> days < 1) {
                if ($diff -> h < 1) {
                    if ($diff -> i <= 1) {
                        return "1 minute ago";
                    }
                    
                    return "{$diff -> i} minutes ago";
                    
                } else if ($diff -> h == 1) {
                    return "1 hour ago";
                }
                
                return "{$diff -> h} hours ago";
                
            } else if ($diff -> days == 1) {
                return "1 day ago";
            } else if ($diff -> days < 6) {
                return "{$diff -> days} days ago";
            }
    
            // Else
    
            [$year, $month, $day] = explode('-', $date);
    
            $months_list = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    
            $formated_date = $day . ' ' . $months_list[$month - 1] . ' ' . $year;
    
            return $formated_date;
    
        }


        public function format_text($text) {
            $formated_text = preg_replace('/\*\*(.+)?\*\*/', '<b>\1</b>', $text);
            $formated_text = preg_replace('/# (.+)?\n/', '<h2 class="text-lg block font-semibold">\1</h2>', $formated_text);
            return $formated_text;
        }
    }

    