<?php
require_once '../database/pdo.php';

$role_sql = "SELECT id FROM role WHERE role_name = 'admin'";
$role_stmt = $pdo->prepare($role_sql);
$role_stmt->execute();
$role = $role_stmt->fetch(PDO::FETCH_ASSOC);
$role_id = $role['id'];

$full_name = "Admin";
$township_id = 7;
$email     = "admin@findmypet.com";
$password  = "Admin@123";
$phone     = "09123456789";
$address   = "FindMyPet HQ, Myanmar";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (role_id, township_id, full_name, email, password, phone, address, is_verified,  status) 
        VALUES (:role_id, :township_id, :full_name, :email, :hashed_password, :phone, :address, :is_verified, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'role_id' => $role_id,
    'township_id' => $township_id,
    'full_name' => $full_name,
    'email' => $email,
    'hashed_password' => $hashed_password,
    'phone' => $phone,
    'address' => $address,
    'is_verified'     => 1,     
    'status'          => 'Active'
]);