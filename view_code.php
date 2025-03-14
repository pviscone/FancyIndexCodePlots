<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure 'file' parameter exists
if (!isset($_GET['file'])) {
    die("No file specified.");
}

// Get the requested file path securely
$file = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $_GET['file']);

// Ensure the file exists and is inside the web root
if (!$file || !file_exists($file) || strpos($file, realpath($_SERVER['DOCUMENT_ROOT'])) !== 0) {
    die("File not found or access denied.");
}

// Serve raw file if "raw" parameter is set
if (isset($_GET['raw'])) {
    $filename = basename($_GET['file']); // Extract just the filename

    // Set headers for correct file download
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Length: " . filesize($file));
    readfile($file);
    exit;
}



// Map extensions to highlight.js languages
$extension_map = [
    'py'   => 'python', 'cpp'  => 'cpp', 'c'    => 'c', 'h'    => 'cpp', 'hpp'  => 'cpp',
    'java' => 'java', 'js'   => 'javascript', 'ts'   => 'typescript',
    'css'  => 'css', 'sh'   => 'bash', 'bat'  => 'dos', 'md'   => 'markdown', 
    'xml'  => 'xml', 'yml'  => 'yaml', 'yaml' => 'yaml', 'rs'   => 'rust',
    'go'   => 'go', 'swift'=> 'swift', 'kt'   => 'kotlin', 'm'    => 'objectivec', 'r'    => 'r',
    'sql'  => 'sql', 'lua'  => 'lua', 'pl'   => 'perl', 'rb'   => 'ruby', 'cs'   => 'csharp',
    'asm'  => 'x86asm'
];

// Get file extension and determine language
$ext = pathinfo($file, PATHINFO_EXTENSION);
$language = $extension_map[$ext] ?? 'plaintext';

// Read file content safely
$content = htmlspecialchars(file_get_contents($file));

header("Content-Type: text/html");

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Viewing: " . htmlspecialchars($_GET['file']) . "</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.css'>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/highlightjs-line-numbers.js/2.8.0/highlightjs-line-numbers.min.js'></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            background-color: #ffffff; 
            color: #000000; 
        }
        .container { 
            max-width: 100%; 
            padding: 20px; 
        }
        h2 { 
            font-size: 20px; 
            margin-left: 10px; 
        }
        button { 
            margin: 10px; 
            padding: 8px 12px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px;
        }
        button:hover { 
            background-color: #0056b3; 
        }
        pre { 
            background: #f5f5f5; 
            padding: 20px; 
            border-radius: 5px; 
            white-space: pre; /* Wrap long lines */
            word-wrap: normal; 
            width: calc(100% - 40px); /* Full width */
            margin: 10px auto; /* Center */
            overflow-x: auto; /* Horizontal scroll if needed */
            line-height: 1.2; /* Decrease spacing between lines */
        }
        /* Line numbers styling */
        .hljs-ln { 
            border-collapse: collapse; 
        }
        .hljs-ln td { 
            padding: 2px; /* Decrease padding for tighter spacing */
            border-right: 2px solid #ccc; 
        }
        .hljs-ln-numbers { 
            color: #888; 
            text-align: right; 
            user-select: none; 
            width: 15px; 
        }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Viewing: " . htmlspecialchars($_GET['file']) . "</h2>
        <button onclick=\"window.location.href='?file=" . urlencode($_GET['file']) . "&raw=1'\">Download</button>
        <button onclick=\"copyWgetCommand()\">Copy Wget Command</button>
        <input type='text' id='wgetCommand' value=\"wget --content-disposition '" . htmlspecialchars((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/view_code.php?file=" . urlencode($_GET['file']) . "&raw=1") . "'\" style='position: absolute; left: -9999px;'>
        <pre><code class='$language hljs'>$content</code></pre>
    </div>

    <script>
        function copyWgetCommand() {
            var wgetInput = document.getElementById('wgetCommand');
            wgetInput.select();
            wgetInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
        }
        hljs.highlightAll();
        hljs.initLineNumbersOnLoad();
    </script>
</body>
</html>";
?>
