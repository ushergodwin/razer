<?php
namespace System\Http\Response\Alert;
class Alert
{

    public function success($message) {
        return "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong><i class='fas fa-check-circle text-success'></i></strong> {$message}
            </div>
            ";
    }

    public function failure($message) {
        return "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
            <strong><i class='fas fa-exclamation-triangle text-warning'></i></strong> {$message}
            </div>
            ";
    }

    public function info($message) {
        return "<div class='alert alert-info alert-dismissible fade show' role='alert'>
            <strong><i class='fas fa-info-circle text-info'></i></strong> {$message}
            </div>
            ";
    }

    public function danger($message) {
        return "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong><i class='fas fa-exclamation-triangle text-danger'></i></strong> {$message}
            </div>
            ";
    }
    
}
