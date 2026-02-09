<?php
use PHPUnit\Framework\TestCase;

// Volvemos a incluir el archivo directamente para evitar problemas con el autoloading en este entorno.
require_once __DIR__ . '/../models/User.php';

class UserTest extends TestCase
{
    private $pdo;
    private $user;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("CREATE TABLE usuarios (
            id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre VARCHAR(150) NOT NULL,
            apellido1 VARCHAR(150) NOT NULL,
            apellido2 VARCHAR(150),
            provincia VARCHAR(50) NOT NULL,
            localidad VARCHAR(100) NOT NULL,
            cp VARCHAR(10) NOT NULL,
            calle VARCHAR(150) NOT NULL,
            numero VARCHAR(10) NOT NULL,
            telefono VARCHAR(20),
            email VARCHAR(80) NOT NULL UNIQUE,
            tipo_doc VARCHAR(3) NOT NULL,
            num_doc VARCHAR(15) NOT NULL,
            fecha_nacimiento DATE NOT NULL,
            pass VARCHAR(255) NOT NULL,
            tipo_usu VARCHAR(1) NOT NULL
        );");

        $this->user = new User($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        $this->user = null;
    }

    public function testCanRegisterUser()
    {
        $userData = [
            'nombre' => 'Test',
            'apellido1' => 'User',
            'apellido2' => 'PHPUnit',
            'provincia' => 'Test Province',
            'localidad' => 'Test City',
            'cp' => '12345',
            'calle' => 'Test Street',
            'numero' => '123',
            'telefono' => '123456789',
            'email' => 'test@example.com',
            'tipo_doc' => 'DNI',
            'num_doc' => '12345678Z',
            'fecha_nacimiento' => '2000-01-01',
            'password' => 'password123'
        ];

        $result = $this->user->register($userData);

        $this->assertTrue($result);

        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute(['test@example.com']);
        $userInDb = $stmt->fetch();

        $this->assertNotFalse($userInDb, "El usuario no se encontró en la BD.");
        $this->assertEquals('Test', $userInDb['nombre']);
        $this->assertTrue(password_verify('password123', $userInDb['pass']));
    }

    /**
     * Test para verificar que un usuario registrado puede iniciar sesión.
     */
    public function testCanLoginUser()
    {
        // Primero, necesitamos un usuario registrado.
        $userData = [
            'nombre' => 'Login', 'apellido1' => 'Test', 'apellido2' => '',
            'provincia' => 'Prov', 'localidad' => 'Loc', 'cp' => '12345',
            'calle' => 'Calle', 'numero' => '1', 'telefono' => '987654321',
            'email' => 'login@example.com', 'tipo_doc' => 'DNI', 'num_doc' => '87654321Z',
            'fecha_nacimiento' => '1990-01-01', 'password' => 'correct-password'
        ];
        $this->user->register($userData);

        // Ahora, intentamos hacer login con las credenciales correctas.
        $loggedInUser = $this->user->login('login@example.com', 'correct-password');

        // Afirmamos que el login fue exitoso y nos devolvió los datos del usuario.
        $this->assertIsArray($loggedInUser);
        $this->assertEquals('login@example.com', $loggedInUser['email']);
    }

    /**
     * Test para verificar que el login falla con una contraseña incorrecta.
     */
    public function testLoginFailsWithWrongPassword()
    {
        // Primero, registramos un usuario.
        $userData = [
            'nombre' => 'Login', 'apellido1' => 'Test', 'apellido2' => '',
            'provincia' => 'Prov', 'localidad' => 'Loc', 'cp' => '12345',
            'calle' => 'Calle', 'numero' => '1', 'telefono' => '987654321',
            'email' => 'login@example.com', 'tipo_doc' => 'DNI', 'num_doc' => '87654321Z',
            'fecha_nacimiento' => '1990-01-01', 'password' => 'correct-password'
        ];
        $this->user->register($userData);

        // Intentamos hacer login con la contraseña INCORRECTA.
        $loggedInUser = $this->user->login('login@example.com', 'wrong-password');

        // Afirmamos que el resultado es `false`.
        $this->assertFalse($loggedInUser);
    }
}
