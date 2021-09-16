<?php

namespace League\BooBoo\Formatter;

use League\BooBoo\Util;

class HtmlTableFormatter extends AbstractFormatter
{
    /**
     * @var Util\Inspector;
     */
    protected $inspector;

    public function format($e)
    {
        $this->inspector = new Util\Inspector($e);

        if ($e instanceof \ErrorException) {
            return $this->handleErrors($e);
        }

        return $this->formatExceptions($e);
    }

    public function handleErrors(\ErrorException $e)
    {
        $errorString = "<strong>%s</strong>: %s in <strong>%s</strong> on line <strong>%d</strong>";

        $severity = $this->determineSeverityTextValue($e->getSeverity());

        $error = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        $error = sprintf($errorString, $severity, $error, $file, $line);

        return $this->getTable($error);
    }

    protected function formatExceptions($e)
    {
        $errorString = "<strong>Fatal error:</strong> Uncaught exception '%s' %s with message '%s' in %s on line %d";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        if ($e->getCode()) {
            $code = '(' . $e->getCode() . ')';
        } else {
            $code = null;
        }

        $error = sprintf($errorString, $type, $code, $message, $file, $line);
        $result = $this->getTable($error);
        $inspector = $this->inspector;

        if ($inspector->hasPreviousException()) {
            $this->inspector = $inspector->getPreviousExceptionInspector();
            $result = $this->formatExceptions($this->inspector->getException()) . $result;
        }

        return $result;
    }

    protected function processFrame($frame)
    {
        $function = $frame->getClass() ?: '';
        $function .= $frame->getClass() && $frame->getFunction() ? ":" : "";
        $function .= $frame->getFunction() ?: '';

        $fileline = ($frame->getFile() ?: '<#unknown>');
        $fileline .= ':';
        $fileline .= (int)$frame->getLine();

        return [$function, $fileline];
    }

    protected function getTable($error)
    {
        $frames = $this->inspector->getFrames();
        $errorTable = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>PHASER EXCEPTION </title>
            <meta name="description" content="">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        </head>
        <body class="bg-light"> 
        <div class="container-fluid">';

        $errorTable .= '<table class="table table-bordered table-striped table-danger" border="1" cellspacing="0" cellpadding="1"><tr>';
        $errorTable .= '<td colspan="3" style="background: #ff5332">%s</td></tr>';
        $errorTable = sprintf($errorTable, $error);
        $errorTable .= '<tr><td colspan="3">Call Stack</td></tr><tr><th>#</th>';
        $errorTable .= '<th>Function</th><th>Location</th></tr>';

        foreach ($frames as $k => $frame) {
            list($function, $fileline) = $this->processFrame($frame);
            $errorTable .= sprintf(
                '<tr><td width="3%%">%d</td><td width="40%%">%s</td><td>%s</td></tr>',
                $k,
                $function,
                $fileline
            );
        }

        if (count($frames) == 0) {
            $errorTable .= '<tr><td colspan="3"><em>(No stack trace was generated by the error)</em></td>';
        }

        $errorTable .= '
            </table>
            </div>
            </body>
            </html>
        ';
        return $errorTable;
    }
}
