<?php defined('GX_LIB') or die('Direct Access Not Allowed!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= Site::meta(['backend']); ?>
<?= Site::loadLibHeader(); ?>
<style>
*{box-sizing:border-box}body{margin:0;font-family:'Inter',system-ui,sans-serif;background:#f2f2f2;display:flex;align-items:center;justify-content:center;min-height:100vh}
</style>
</head>
<body>
<?php echo System::alert($data ?? []); ?>