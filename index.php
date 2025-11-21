<?php   
if(isset($_GET['Gestor']))
    switch($_GET['Gestor']){
        case 'login':
            include('pages/login.php');
            break;
        case 'register':
            include('pages/register.php');
            break;
            case 'listarMateriais':
            include('pages/listarMateriais.php');
        default:
            include('pages/home.php');
    } 
?>