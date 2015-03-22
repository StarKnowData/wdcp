<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

function mail_send($mail_to, $email_from = '', $email_title = '', $email_content = '')
{
    global $mailsend, $mail_server, $mail_port, $mail_auth, $mail_from, $mail_auth_name, $mail_auth_passwd, $mail_title, $mail_content;

    $maildelimiter = "\n";
    $charset = "gbk";
    if (empty($mail_server) or empty($mail_port)) return;
    if (empty($mail_to)) return;
    if (!empty($email_title)) $mail_title = $email_title;
    if (empty($mail_title)) $mail_title = "wdcp邮件通知";
    if (!empty($email_content)) $mail_content = $email_content;
    if (empty($mail_content)) $mail_content = "wdcp邮件通知";
    if (empty($email_from)) $email_from = $mail_from;
    $mail['mailsend'] = $mailsend;
    $mail['server'] = $mail_server;
    $mail['port'] = $mail_port;
    $mail['auth'] = $mail_auth;
    $mail['auth_username'] = $mail_auth_name;
    $mail['auth_password'] = $mail_auth_passwd;
    $mail['from'] = $mail_from;
    $mailusername = isset($mail_auth_name) ? $mail_auth_name : 1;

    $email_subject = '=?' . $charset . '?B?' . base64_encode(str_replace("\r", '', str_replace("\n", '', '[' . $mail_title . '] ' . $email_subject))) . '?=';
    $email_message = chunk_split(base64_encode(str_replace("\r\n.", " \r\n..", str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $mail_content)))))));

    $email_from = $email_from == '' ? '=?' . $charset . '?B?' . base64_encode($mail_title) . "?= <$adminemail>" : (preg_match('/^(.+?) \<(.+?)\>$/', $email_from, $from) ? '=?' . $charset . '?B?' . base64_encode($from[1]) . "?= <$from[2]>" : $email_from);

    foreach (explode(',', $mail_to) as $touser) {
        $tousers[] = preg_match('/^(.+?) \<(.+?)\>$/', $touser, $to) ? ($mailusername ? '=?' . $charset . '?B?' . base64_encode($to[1]) . "?= <$to[2]>" : $to[2]) : $touser;
    }
    $email_to = implode(',', $tousers);

    $headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: wdcp $version{$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/plain; charset=$charset{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";

    $mail['port'] = $mail_port ? $mail_port : 25;

    if ($mail['mailsend'] == 1 && function_exists('mail')) {
        //echo $email_to."|".trim($email_subject)."|".$email_message."|".$headers;
        @mail($email_to, trim($email_subject), $email_message, $headers);

    } elseif ($mail['mailsend'] == 2) {

        if (!$fp = fsockopen($mail['server'], $mail['port'], $errno, $errstr, 30)) {
            //$errorlog('SMTP', "($mail[server]:$mail[port]) CONNECT - Unable to connect to the SMTP server", 0);
            echo "($mail[server]:$mail[port]) CONNECT - Unable to connect to the SMTP server";
        }
        stream_set_blocking($fp, true);

        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != '220') {
            //$errorlog('SMTP', "$mail[server]:$mail[port] CONNECT - $lastmessage", 0);
            echo "$mail[server]:$mail[port] CONNECT - $lastmessage";
        }

        fputs($fp, ($mail['auth'] ? 'EHLO' : 'HELO') . " wdcp\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
            //$errorlog('SMTP', "($mail[server]:$mail[port]) HELO/EHLO - $lastmessage", 0);
            echo "($mail[server]:$mail[port]) HELO/EHLO - $lastmessage";
        }

        while (1) {
            if (substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
                break;
            }
            $lastmessage = fgets($fp, 512);
        }

        if ($mail['auth']) {
            fputs($fp, "AUTH LOGIN\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 334) {
                //$errorlog('SMTP', "($mail[server]:$mail[port]) AUTH LOGIN - $lastmessage", 0);
                echo "($mail[server]:$mail[port]) AUTH LOGIN - $lastmessage";
            }

            fputs($fp, base64_encode($mail['auth_username']) . "\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 334) {
                //$errorlog('SMTP', "($mail[server]:$mail[port]) USERNAME - $lastmessage", 0);
                echo "($mail[server]:$mail[port]) USERNAME - $lastmessage";
            }

            fputs($fp, base64_encode($mail['auth_password']) . "\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 235) {
                //$errorlog('SMTP', "($mail[server]:$mail[port]) PASSWORD - $lastmessage", 0);
                echo "($mail[server]:$mail[port]) PASSWORD - $lastmessage";
            }

            $email_from = $mail['from'];
        }

        fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from) . ">\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 250) {
            fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from) . ">\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 250) {
                //$errorlog('SMTP', "($mail[server]:$mail[port]) MAIL FROM - $lastmessage", 0);
                echo "($mail[server]:$mail[port]) MAIL FROM - $lastmessage";
            }
        }

        $email_tos = array();
        foreach (explode(',', $email_to) as $touser) {
            $touser = trim($touser);
            if ($touser) {
                fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser) . ">\r\n");
                $lastmessage = fgets($fp, 512);
                if (substr($lastmessage, 0, 3) != 250) {
                    fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser) . ">\r\n");
                    $lastmessage = fgets($fp, 512);
                    //$errorlog('SMTP', "($mail[server]:$mail[port]) RCPT TO - $lastmessage", 0);
                    echo "($mail[server]:$mail[port]) RCPT TO - $lastmessage";
                }
            }
        }

        fputs($fp, "DATA\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 354) {
            //$errorlog('SMTP', "($mail[server]:$mail[port]) DATA - $lastmessage", 0);
            echo "($mail[server]:$mail[port]) DATA - $lastmessage";
        }

        $headers .= 'Message-ID: <' . gmdate('YmdHs') . '.' . substr(md5($email_message . microtime()), 0, 6) . rand(100000, 999999) . '@' . $_SERVER['HTTP_HOST'] . ">{$maildelimiter}";

        fputs($fp, "Date: " . gmdate('r') . "\r\n");
        fputs($fp, "To: " . $email_to . "\r\n");
        fputs($fp, "Subject: " . $email_subject . "\r\n");
        fputs($fp, $headers . "\r\n");
        fputs($fp, "\r\n\r\n");
        fputs($fp, "$email_message\r\n.\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 250) {
            //$errorlog('SMTP', "($mail[server]:$mail[port]) END - $lastmessage", 0);
            echo "($mail[server]:$mail[port]) END - $lastmessage";
        }

        fputs($fp, "QUIT\r\n");

    }
}

//
function uid_to_email($id)
{
    global $db;
    $q = $db->query("select email from wd_member where id='$id'");
    if ($db->num_rows($q) == 0) return '';
    $r = $db->fetch_array($q);
    return $r['email'];
}

function return_email($id)
{
    global $db, $domain, $ip;
    $q = $db->query("select * from wd_mail_tp where mt='$id'");
    $msg = array();
    if ($db->num_rows($q) == 0) {//return $msg;
        $msg['title'] = "";
        $msg['content'] = "";
    } else {
        $r = $db->fetch_array($q);
        $msg['title'] = $r['title'];
        $msg['content'] = str_replace("{url}", $domain, str_replace("{ip}", $ip, str_replace("{username}", $username, $r['content'])));
    }
    return $msg;
}

function mail_tp_list()
{
    global $db;
    $q = $db->query("select * from wd_mail_tp");
    $msg = '<select name="mail_tp_select"><option value="0">选择邮件模板</option>';
    while ($r = $db->fetch_array($q)) {
        $msg .= '<option value="' . $r['mt'] . '">' . $r['title'] . '</option>';
    }
    $msg .= '</select>';
    return $msg;
}

?>
