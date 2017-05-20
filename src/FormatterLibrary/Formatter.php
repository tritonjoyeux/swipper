<?php

namespace Fashiongroup\Swiper\FormatterLibrary;

class Formatter
{
    public function replaceByVoid($content, $terms, $end = null)
    {
        if (!$end) {
            return preg_replace('/' . $terms . '/', '', $content);
        }
        return preg_replace('/' . $terms . '.*?' . $end . '/', '', $content);
    }

    public function replaceByVoidUntilEnd($content, $terms)
    {
        return preg_replace('/' . $terms . '.*/', '', $content);
    }

    public function reduceLineBreak($content, $method = null)
    {
        if (strpos($content, "\r\r\r") !== false) {
            while (strpos($content, "\r\r\r") !== false) {
                $content = str_replace("\r\r\r", "\r\r", $content);
            }
        } elseif (strpos($content, "\r \r \r") !== false) {
            while (strpos($content, "\r \r \r") !== false) {
                $content = str_replace("\r \r \r", "\r \r", $content);
            }
        } elseif (strpos($content, "\n \n \n") !== false) {
            while (strpos($content, "\n \n \n") !== false) {
                $content = str_replace("\n \n \n", "\n \n", $content);
            }
        } elseif (strpos($content, "\n\n\n") !== false) {
            while (strpos($content, "\n\n\n") !== false) {
                $content = str_replace("\n\n\n", "\n\n", $content);
            }
        } else {
            while (strpos($content, "\r\n\r\n\r\n") !== false) {
                $content = str_replace("\r\n\r\n\r\n", "\r\n\r\n", $content);
            }
            $content = str_replace("\r\n", "\n", $content);
            if($method == null) {
                $content = $this->reduceLineBreak($content, "smth");
            }
        }
        return $content;
    }

    public function multipleReplaceBy($content, $terms)
    {
        foreach ($terms as $key => $term) {
            $content = preg_replace('/' . $key . '/', $term, $content);
        }
        return $content;
    }

    public function replaceBy($content, $term, $by)
    {
        return preg_replace('/'.$term.'/', $by, $content);
    }

    public function replaceEndAndStartLineBreak($content)
    {
        return rtrim($content);
    }
}
