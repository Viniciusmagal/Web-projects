<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eleições</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  </head>
  <body>
    <h1>Eleições 2022</h1>
    <?php

// conecta o banco de dados
$banco = new PDO('mysql:host=localhost;dbname=2dsb_vinicius_magalhaes_eleicao', "aluno", "etec@147",
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));


// testar se a pagina foi chamada pelo formulario
if(isset($_POST["gravar"])) {
  if($_POST["id"]=="") {
    $sql = "INSERT INTO tb03_candidato
    (tb03_nome, tb03_cpf)
    VALUES (?, ?)";
    $comando = $banco->prepare($sql);
    $comando->execute(array($_POST["nome"], $_POST["cpf"]));
  } else {
    $sql = "UPDATE tb03_candidato SET
    tb03_nome = ?, tb03_cpf = ?
    WHERE tb03_cpf = ?";
    $comando = $banco->prepare($sql);
    $comando->execute(array($_POST["nome"], 
                  $_POST["cpf"], $_POST["id"]));
  }
}
if(isset($_GET["excluir"])) {
  $sql = "DELETE FROM tb03_candidato
  WHERE tb03_cpf = ?";
  $comando = $banco->prepare($sql);
  $comando->execute(array($_GET["excluir"]));

}

// prepara um comando
$sql = "SELECT * FROM tb03_candidato 
LEFT JOIN tb04_eleicao on (tb04_cpf=tb03_cpf)
LEFT JOIN tb01_partido ON (tb04_cod_partido=tb01_cod_partido)
LEFT JOIN tb02_cargo ON (tb04_cod_cargo=tb02_cod_cargo)";
$consulta = $banco->prepare($sql);

// executa o comando
$consulta->execute();

echo "<table class='table'>";
// mostra o resultado
while($registro = $consulta->fetch()) {
    echo "<tr>";
    echo "<td>".$registro["tb03_cpf"]."</td>";
    echo "<td>".$registro["tb03_nome"]."</td>";
    echo "<td>".$registro["tb01_nome"]."</td>";
    echo "<td>".$registro["tb02_nome"]."</td>";
    echo '<td><img width="200" height="300" src="data:image/jpeg;base64,'.base64_encode($registro['tb03_foto']).'"/></td>';
    echo '<td><a href="sql.php?cpf='.$registro["tb03_cpf"].'#formulario" class="btn btn-primary"><i class="bi bi-pencil-fill"></i> Editar</a></td>';
    echo '<td><button onclick="confirmaExclusao(\'sql.php?excluir='.$registro["tb03_cpf"].'\')" class="btn btn-primary"><i class="bi bi-trash-fill"></i> Excluir</button></td>';
    echo "</tr>";
}
echo "</table>";

$tb03_cpf = "";
$tb03_nome = "";
if(isset($_GET["cpf"])) {
  $sql = "SELECT * FROM tb03_candidato 
    WHERE tb03_cpf = ?";
  $consulta = $banco->prepare($sql);

 
  $consulta->execute(array($_GET["cpf"]));
  if($registro = $consulta->fetch()) {
    extract($registro);
  }
}
?>

<a name="formulario"></a>
<form action="sql.php" method="post">
    <input type="hidden" name="id" value="<?php echo $tb03_cpf; ?>">
    <br>CPF:
    <br><input type="text" name="cpf" value="<?php echo $tb03_cpf; ?>">
    <br>Nome do candidato:
    <br><input type="text" name="nome" value="<?php echo $tb03_nome; ?>">
    <br><input type="submit" name="gravar" value="Gravar">
</form>

<p>&nbsp;</p>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

    <script>
      function confirmaExclusao(link) {
        if(confirm("Tem certeza que deseja excluir?")) {
          window.location = link
        }
      }
    </script>
  </body>
</html>
