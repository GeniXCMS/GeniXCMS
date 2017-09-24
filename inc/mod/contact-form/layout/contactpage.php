
<?=Hooks::run('contact_page_notification', $data);?>
<h2 class="blog-post-title">Contact Us
<hr>
</h2>
<form action="" method="post">
<div class="form-group">
    <label>Name</label>
    <input type="text" class="form-control" name="name">
</div>
<div class="form-group">
    <label>E-Mail</label>
    <input type="email" class="form-control" name="email">
</div>
<div class="form-group">
    <label>Subject</label>
    <input type="text" class="form-control" name="subject">
</div>
<div class="form-group">
    <label>Message</label>
    <textarea class="form-control" name="message" rows="10"></textarea>
</div>
<?=Xaptcha::html(); ?>
<div class="form-group">
    <input type="submit" class="btn btn-success" name="sendMessage" value="Send Message">
    <input type="hidden" name="token" value="<?=TOKEN;?>">
</div>
</form>
