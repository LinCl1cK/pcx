<?php
// app/core/debug_helper.php

function globalErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='background: #fee; border: 2px solid #f00; padding: 20px; margin: 20px; font-family: monospace;'>";
    echo "<h3 style='color: #900;'>⚠️ PHP Error Detected</h3>";
    echo "<b>Message:</b> $errstr<br>";
    echo "<b>File:</b> $errfile<br>";
    echo "<b>Line:</b> $errline<br>";
    echo "<p><i>Copy this message and paste it into the AI for a fix!</i></p>";
    echo "</div>";
    return true;
}

// Set this function to catch all errors
set_error_handler("globalErrorHandler");

// Catch "Fatal" errors that usually cause the white screen of death
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
        globalErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    }
});