<?php

abstract class User {
    private string $name;
    private string $email;

    public function __construct(string $name, string $email) {
        $this->name = $name;
        $this->email = $email;
    }

    abstract public function getRole(): string;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }
}

class Student extends User {
    private string $group;

    public function __construct(string $name, string $email, string $group) {
        parent::__construct($name, $email);
        $this->group = $group;
    }

    public function getRole(): string {
        return 'Студент';
    }

    public function getGroup(): string {
        return $this->group;
    }

    public function setGroup(string $group): void {
        $this->group = $group;
    }
}

class Teacher extends User {
    private string $subject;

    public function __construct(string $name, string $email, string $subject) {
        parent::__construct($name, $email);
        $this->subject = $subject;
    }

    public function getRole(): string {
        return 'Викладач';
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }
}

$student = new Student('Іван Петренко', 'ivan@example.com', 'КН-21');
$teacher = new Teacher('Олена Іваненко', 'olena@example.com', 'Програмування');

function renderUserInfo(User $user): void {
    echo '<div style="margin-bottom:12px; padding:8px; border:1px solid #ddd;">';
    echo 'Ім’я: ' . htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8') . '<br>';
    echo 'Email: ' . htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8') . '<br>';
    echo 'Роль: ' . htmlspecialchars($user->getRole(), ENT_QUOTES, 'UTF-8') . '<br>';

    if ($user instanceof Student) {
        echo 'Група: ' . htmlspecialchars($user->getGroup(), ENT_QUOTES, 'UTF-8');
    } elseif ($user instanceof Teacher) {
        echo 'Предмет: ' . htmlspecialchars($user->getSubject(), ENT_QUOTES, 'UTF-8');
    }
    echo '</div>';
}

?><!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Користувачі (ООП)</title>
</head>
<body>
<?php
renderUserInfo($student);
renderUserInfo($teacher);
?>
</body>
</html>
