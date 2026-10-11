<?php
session_start();

$name     = trim($_POST['name'] ?? '');
$memberId = trim($_POST['member_id'] ?? '');
$address  = trim($_POST['address'] ?? '');
$phone    = trim($_POST['phone'] ?? '');

$errors = [];

if ($name === '') {
    $errors[] = "Name is required.";
} elseif (!preg_match("/^[\p{L} .'-]+$/u", $name)) {
    $errors[] = "Name can only contain letters, spaces, and basic punctuation.";
}

if ($memberId === '') {
    $errors[] = "Member ID is required.";
} elseif (!preg_match('/^\d+$/', $memberId)) {
    $errors[] = "Member ID can only contain digits.";
} else {
    foreach ($_SESSION['members'] ?? [] as $member) {
        if ((string) $member['member_id'] === $memberId) {
            $errors[] = "Member ID is already registered.";
            break;
        }
    }
}

if ($address === '') {
    $errors[] = "Address is required.";
}

if ($phone !== '') {
    if (!preg_match('/^[0-9+\-\s]+$/', $phone)) {
        $errors[] = "Phone number can only contain numbers, spaces, plus, and hyphens.";
    } else {
        $cleanPhone = str_replace([' ', '-'], '', $phone);
        if (!preg_match('/^(\+62|0)[0-9]{8,13}$/', $cleanPhone)) {
            $errors[] = "Phone number must start with 0 or +62 and be 9-14 digits long (e.g. 081234567890).";
        }
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => implode(' ', $errors)];
    header('Location: add.php');
    exit;
}

if (!isset($_SESSION['members'])) {
    $_SESSION['members'] = [];
}

$_SESSION['members'][] = [
    'name'      => $name,
    'member_id' => $memberId,
    'address'   => $address,
    'phone'     => $phone,
];

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Member added successfully.'];
header('Location: list.php');
exit;