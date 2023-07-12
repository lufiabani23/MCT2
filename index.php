<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Importações Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


    <link rel="stylesheet" type="text/css" href="css/login.css">

    <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">

    <title>SistemaPsico</title>
</head>

<body class="login">

    <div class="login-form">
        <form action="autenticar.php" method="POST">
            <div class="logo">
                <img src="img/logosistemapsico.png" alt="SistemaPsico">
            </div>
            <h2 class="text-center">
                Entre no Sistema
            </h2>
            <div class="form-group">
                <input class="form-control mb-2" type="email" name="usuario" placeholder="Insira seu e-mail" value="" required>
            </div>

            <div class="form-group">
                <input class="form-control mb-2" type="password" name="senha" placeholder="Insira sua senha" value="" required>
            </div>

            <div class="form-group d-grid gap-2">
                <input class="btn btn-primary btn-lg btn-block" type="submit" name="btn-login" value="Login">
            </div>

            <div class="clearfix">
                <label class="float-left checkbox-inline">
                    <input type="checkbox">
                    Lembrar-me
                </label>
                <a data-toggle="modal" data-target="#modalExemplo" class="float-right">
                    Recuperar senha
                </a>

            </div>
        </form>
    </div>

    <div class="modal fade" id="modalExemplo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Recuperar senha</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Esqueceu sua senha? <br>
                    Entre em contato com o suporte e recupere. <br>
                    E-mail: <a href="mailto:luisfiabani@gmail.com">luisfiabani@gmail.com</a> <br>
                    Atendimento de segunda à sexta das 08h às 17h.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>