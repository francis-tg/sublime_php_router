<?php 
namespace Francis\SublimePhp\Interfaces;

interface UserInterface{
    function addUser(array $user);
    function editUser(array $user);
    function deleteUser(array $user);
}