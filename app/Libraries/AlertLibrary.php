<?php

namespace App\Libraries;

class AlertLibrary
{
    /**
     * Display validation errors in a list
     *
     * @param array $errors Array of error messages
     * @return string HTML output
     */
    public function displayErrors(array $errors): string
    {
        $html = '<ul class="mb-0">';
        foreach ($errors as $error) {
            $html .= '<li>' . esc($error) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
