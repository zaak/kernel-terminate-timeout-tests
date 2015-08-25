<?php

namespace Test;


class Response extends \Symfony\Component\HttpFoundation\Response
{
    public function send()
    {
        if (function_exists('fastcgi_finish_request')) {
            $this->sendHeaders();
            $this->sendContent();
            fastcgi_finish_request();
        } elseif ('cli' !== PHP_SAPI) {
            $this->headers->add(['Connection' => 'close']);
            $this->headers->add(['Content-Encoding' => 'none']);

            ignore_user_abort(true);
            set_time_limit(0);

            $this->sendHeaders();
            $this->sendContent();

            flush();
            ob_flush();

            session_write_close();
        }

        return $this;

    }
}