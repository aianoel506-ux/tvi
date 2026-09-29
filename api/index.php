<?php
/**
 * Vercel Serverless Function entry point for CodeIgniter 3
 */

// Set script environment variables for CodeIgniter router
$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__) . '/index.php';
$_SERVER['SCRIPT_NAME']     = '/index.php';

// Change current working directory to the project root
chdir(dirname(__DIR__));

// Bootstrap CodeIgniter
require dirname(__DIR__) . '/index.php';
