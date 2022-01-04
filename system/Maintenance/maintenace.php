<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> MAINTENANCE</title>
    <!-- Bootstrap CSS file -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-body shadow mt-1">
                    <div class="row">
                        <div class="col-lg-6">
                            <h4><?= APP_NAME ?> will be back soon.</h4>
                            <p>We are busy upgrading <?= APP_NAME ?> with  
                                new technologies and features to add even more ways for 
                            you to leverage your experience with us. We will be back within 
                             in a blink.
                             <br/>
                             We apologize for the inconvenience and appreciate your 
                             patience. Thank you for using <?= APP_NAME ?>
                            </p>
                        </div>
                        <div class="col-lg-6">
                            <img src="<?= url('maintenance.jpg') ?>" class="img-responsive"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>