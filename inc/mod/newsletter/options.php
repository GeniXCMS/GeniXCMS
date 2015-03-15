<?php

if(isset($_POST['sendmail'])){
    // check token first
    if ( !isset($_POST['token']) || !Token::isExist($_POST['token']) ) {
        # code...
        $alertred[] = TOKEN_NOT_EXIST;
    }
    if(isset($alertred)){
        $data['alertred'] = $alertred;
    }else{
        $subject = Typo::cleanX($_POST['subject']);
        $msg = $_POST['message'];

        if ($_POST['type'] == 'text') {
            # code...
            $msg = str_replace('<br>', "\r\n\r\n", $msg);
            $msg = str_replace('</p><p>', "\r\n\r\n", $msg);
            $msg = str_replace('&nbsp;', " ", $msg);
            $msg = strip_tags($msg);
        }else{
            $msg = $msg;
        }

        $msg = str_replace('{{sitename}}', Site::$name, $msg);
        $msg = str_replace('{{siteurl}}', Site::$url, $msg);
        $msg = str_replace('{{sitemail}}', Site::$email, $msg);

        if($_POST['recipient'] == ''){
            $usr = Db::result("SELECT * FROM `user`");
            foreach ($usr as $u) {
                # code...
                $msgs = str_replace('{{userid}}', $u->userid, $msg);
                $vars = array(
                            'to' => $u->email,
                            'to_name' => $u->userid,
                            'message' => $msgs,
                            'subject' => $subject,
                            'msgtype' => $_POST['type']
                        );
                $mailsend = Mail::send($vars);
                if($mailsend !== null){
                    $alertmailsend[] = $mailsend;
                }
                sleep(3);
            }
        }elseif($_POST['recipient'] != ''){
            $usr = Db::result("SELECT * FROM `user` WHERE `group` = '{$_POST['recipient']}'");
            foreach ($usr as $u) {
                # code...
                $msgs = str_replace('{{userid}}', $u->userid, $msg);
                $vars = array(
                            'to' => $u->email,
                            'to_name' => $u->userid,
                            'message' => $msgs,
                            'subject' => $subject,
                            'msgtype' => $_POST['type']
                        );
                $mailsend = Mail::send($vars);
                if($mailsend !== null){
                    $alermailsend[] = $mailsend;
                }
                sleep(3);
            }
        }
        if (isset($alertmailsend)) {
            $data['alertred'] = $alertmailsend;
        }else{
            $data['alertgreen'][] = "Success Sending Email";
        }
        
    }
}


if (isset($data['alertgreen'])) {
    # code...
    echo "<div class=\"alert alert-success\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\">Close</span>
    </button>";
    foreach ($data['alertgreen'] as $alert) {
        # code...
        echo "$alert\n";
    }
    echo "</div>";
}
if (isset($data['alertred'])) {
    # code...
    echo "<div class=\"alert alert-danger\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\">Close</span>
    </button>";
    foreach ($data['alertred'] as $alert) {
        # code...
        echo "$alert\n";
    }
    echo "</div>";
}

?>

<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-envelope-o"></i> NewsLetter 
        <small class="pull-right">Send NewsLetter to All members</small>
        </h1>
        <hr />
    </div>
    <form action="" method="post">
    <div class="col-md-6">
        
        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control">
        </div>
        <div class="form-group">
            <label>Recipients</label>
            <select name="recipient" class="form-control">
                <option value="">All Members</option>
                <option value="0">Administrators</option>
                <option value="4">General Members</option>
            </select>
        </div>
        <div class="form-group">
            <label>Mail Type</label>
            <select name="type" class="form-control">
                <option value="text">Plain Text</option>
                <option value="html">HTML</option>
            </select>
        </div>
        <div class="form-group">
            <input type="hidden" name="token" value="<?=TOKEN;?>">
            <button type="submit" name="sendmail" class="btn btn-primary">
                <i class="fa fa-envelope"></i> Send Email
            </button>
        </div>

        <div class="form-group">
            <label>eMail Tags</label>
            <ul class="list-group clearfix">
                <li class="list-group-item col-md-6"><strong>{{userid}}</strong> : Member Userid</li>
                <li class="list-group-item col-md-6"><strong>{{sitename}}</strong> : Website Name</li>
                <li class="list-group-item col-md-6"><strong>{{siteurl}}</strong> : Website URL</li>
                <li class="list-group-item col-md-6"><strong>{{sitemail}}</strong> : Website Email</li>
            </ul>
        </div>
        
    </div>
    <div class="col-md-6">
        <label>Message</label>
        <textarea name="message" class="form-control editor" id="editor"></textarea>
    </div>
    </form>
</div>