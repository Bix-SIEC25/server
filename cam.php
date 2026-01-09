<?php
// session_start();

// Configuration
$upload_dir = __DIR__ . '/uploads/';             // consider placing this outside webroot in production
define('MAX_FILE_SIZE_BYTES', 5 * 1024 * 1024);  // 5 MB
// allowed image types (exif_imagetype constants) => extension
$allowed_image_types = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG  => 'png',
    IMAGETYPE_GIF  => 'gif',
];

if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        http_response_code(500);
        echo "Server configuration error: cannot create upload directory.";
        exit;
    }
}

// prevent code execution and listing
$htaccess_path = $upload_dir . '.htaccess';
if (!file_exists($htaccess_path)) {
    @file_put_contents(
        $htaccess_path,
        "Options -Indexes\n" .
            "<FilesMatch \"\\.(php|php[0-9]|phtml|phar|pl|py|jsp|asp|aspx|sh|cgi|js)$\">\n" .
            "  Require all denied\n" .
            "</FilesMatch>\n"
    );
}

header('X-Content-Type-Options: nosniff');

$errors = [];
$result = null;

function sanitize_text_field(string $s): string
{
    $s = substr($s, 0, 64);
    return preg_replace('/[^A-Za-z0-9_-]/', '_', $s);
}

// POST handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image']) || !isset($_POST['popop'])) {
        $errors[] = 'No file uploaded (expected field name "image"). (or no popop)';
        echo 'No file uploaded (expected field name "image"). (or no popop)';
        header("Location: ./ko");
        exit;
    }
    $kek = file_get_contents('../.bix', false);
    if (trim(htmlspecialchars($_POST['popop'])) != trim($kek)) {
        $errors[] = 'No file uploaded (expected field name "image").';
	#error_log("ERR: " . trim($kek) . " != '" . trim(htmlspecialchars($_POST['popop'])) . "'\n (true)\n", 3, "/var/www/bix.ovh/uploads/errrrrrrr");
        header("Location: ./ko");
        exit;
    }
    $f = $_FILES['image'];

    // Basic upload error check
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload failed (error code: ' . intval($f['error'] ?? -1) . ').';
        header("Location: ./ko");
        exit;
    }
    // Size check
    if (!isset($f['size']) || $f['size'] > MAX_FILE_SIZE_BYTES) {
        $errors[] = 'File exceeds maximum allowed size of ' . (MAX_FILE_SIZE_BYTES / 1024 / 1024) . ' MB.';
        header("Location: ./ko");
        exit;
    }
    // Ensure this is an uploaded file
    if (!is_uploaded_file($f['tmp_name'])) {
        $errors[] = 'Possible file upload attack detected.';
        header("Location: ./ko");
        exit;
    }
    // Verify actual image type using exif_imagetype (reads binary signature)
    $img_type = @exif_imagetype($f['tmp_name']);
    if ($img_type === false || !array_key_exists($img_type, $allowed_image_types)) {
        $errors[] = 'Uploaded file is not a supported image type.';
        header("Location: ./ko");
        exit;
    }
    // Optionally cross-check MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $f['tmp_name']);
    finfo_close($finfo);
    $expected_mime = match ($img_type) {
        IMAGETYPE_JPEG => 'image/jpeg',
        IMAGETYPE_PNG  => 'image/png',
        IMAGETYPE_GIF  => 'image/gif',
        default => ''
    };
    if ($expected_mime && $mime !== $expected_mime) {
        // Not strictly necessary to reject here, but note mismatch
        // We'll reject to be strict.
        $errors[] = 'MIME type mismatch.';
        header("Location: ./ko");
        exit;
    }
    // sanitize provided small fields, and derive ext from type
    $ts_input = $_POST['timestamp'] ?? '';
    // prefer numeric-like timestamp or fallback to server time
    $ts = preg_replace('/[^0-9_\-T:]/', '_', substr($ts_input, 0, 64));
    if ($ts === '') $ts = date('Ymd_His');

    // strong random suffix
    try {
        $rand = bin2hex(random_bytes(8));
    } catch (Exception $e) {
        $rand = substr(md5(uniqid('', true)), 0, 16);
    }

    $ext = $allowed_image_types[$img_type];
    $filename = sprintf('last.%s', $ext); //sprintf('%s_%s.%s', $ts, $rand, $ext);
    $destination = $upload_dir . $filename;

    // move uploaded file safely
    if (!move_uploaded_file($f['tmp_name'], $destination)) {
        $errors[] = 'Failed to move uploaded file.';
        header("Location: ./ko");
        exit;
    }
    // Set strict file permissions
    @chmod($destination, 0644);

    // Optional: call a malware scanner (ClamAV) here for production systems.

    @file_get_contents(
        "http://127.0.0.1:6442/push",
        false,
        stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: text/plain\r\n",
            'content' => "bix/img:new"
        ]])
    );

    header("Location: ./ok");
    exit;
    // $result = [
    //     'filename' => $filename,
    //     'url' => 'uploads/' . rawurlencode($filename),
    //     'mime' => $mime,
    //     'timestamp' => $ts,
    //     'size' => filesize($destination),
    // ];
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bix car</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .box {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
            max-width: 900px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .gallery img {
            max-width: 200px;
            margin: 6px;
            border: 1px solid #ccc;
            padding: 3px;
            background: #fff;
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>Upload Receiver</h2>

        <?php if ($errors): ?>
            <div style="color: red;">
                <strong>Errors:</strong>
                <ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</li>'; ?></ul>
            </div>
        <?php endif; ?>

        <?php if ($result): ?>
            <h3>Saved</h3>
            <p>File: <?php echo htmlspecialchars($result['filename']); ?></p>
            <p>MIME: <?php echo htmlspecialchars($result['mime']); ?></p>
            <p>Timestamp: <?php echo htmlspecialchars($result['timestamp']); ?></p>
            <div>
                <img src="<?php echo htmlspecialchars($result['url']); ?>" alt="Uploaded image">
            </div>
            <hr>
            <p><a href="<?php echo htmlspecialchars($result['url']); ?>" target="_blank" rel="noreferrer noopener">Open raw image</a></p>
        <?php else: ?>
            <h3>Send image via HTTP POST</h3>
            <p>POST a file field named <code>image</code> and optional field <code>timestamp</code>.</p>
        <?php endif; ?>

        <h4>Browser test form</h4>
        <form method="post" enctype="multipart/form-data">
            <label>Image file: <input type="file" name="image" accept="image/*" required></label><br>
            <label>Timestamp: <input type="text" name="timestamp" value="<?php echo htmlspecialchars(date('Ymd_His')); ?>"></label><br>
            <button type="submit">Upload</button>
        </form>
    </div>

    <div class="box" style="margin-top: 18px;">
        <h3>Gallery (latest uploads)</h3>
        <div class="gallery">
            <?php
            $files = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            if ($files) {
                usort($files, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $max_show = 24;
                $count = 0;
                foreach ($files as $f) {
                    if ($count++ >= $max_show) break;
                    $fname = basename($f);
                    // rawurlencode filename in URL; escape for HTML attributes
                    $url = 'uploads/' . rawurlencode($fname);
                    echo '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noreferrer noopener"><img src="' . htmlspecialchars($url) . '" alt=""></a>';
                }
            } else {
                echo '<p>No uploads yet.</p>';
            }
            ?>
        </div>
    </div>

</body>

</html>
