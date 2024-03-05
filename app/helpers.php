<?php

if ( ! function_exists('put_permanent_env'))
{
    function put_permanent_env($key, $value)
    {
        $path = app()->environmentFilePath();
        $envContents = file_get_contents($path);

        // Escape special characters in the key for use in a regular expression
        $escapedKey = preg_quote($key, '/');

        // Check if the key already exists in the file
        if (preg_match("/^{$escapedKey}=/m", $envContents)) {
            // Key exists, replace its value
            $envContents = preg_replace(
                "/^{$escapedKey}=.*/m",
                "{$key}={$value}",
                $envContents
            );
        } else {
            // Key does not exist, append it to the file
            $envContents .= "\n{$key}={$value}\n"; // Ensure there's a newline before and after the new entry
        }

        // Write the modified contents back to the file
        file_put_contents($path, $envContents);
    }
}