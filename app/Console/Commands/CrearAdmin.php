<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CrearAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crear-admin';

    /**
     * The console command description.
     *
     * @var string
     */
     protected $description = 'Crear un usuario administrador';

    /**
     * Execute the console command.
     */
    public function handle(): int
{

    $intentosMaximos = 3;
    $intentos = 0;

    while ($intentos < $intentosMaximos) {
        $clave = $this->secret('🔐 Ingresa la contraseña para ejecutar este comando');

        if ($clave === env('ADMIN_COMMAND_PASSWORD')) {
            $this->info('✅ Acceso concedido.');
            break; // Salir del bucle si la contraseña es correcta
        }

        $intentos++;
        $this->error("❌ Contraseña incorrecta. Intento $intentos de $intentosMaximos.");

        if ($intentos >= $intentosMaximos) {
            $this->error('🔒 Demasiados intentos fallidos. Acceso denegado.');
            return Command::FAILURE;
        }

        // Pausa de 3 segundos antes del siguiente intento
        sleep(3);
    }

    $email = $this->ask('Correo del administrador');
    $nombre = $this->ask('Nombre');
    $apellido = $this->ask('Apellido');
    $celular = $this->ask('Celular');
    $password = $this->secret('Contraseña');

    // Validaciones
    $data = compact('nombre', 'apellido', 'email', 'celular', 'password');

    $rules = [
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',
        'email' => 'required|string|email|max:100|unique:users,email',
        'password' => 'required|string|min:8',
        'celular' => 'required|digits:10|regex:/^[0-9]+$/|unique:users,celular',
    ];

    $messages = [
        'nombre.string' => 'El nombre debe ser una cadena de texto.',
        'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
        'nombre.required' => 'El nombre es obligatorio.',

        'apellido.string' => 'El apellido debe ser una cadena de texto.',
        'apellido.max' => 'El apellido no puede tener más de 100 caracteres.',
        'apellido.required' => 'El apellido es obligatorio.',

        'email.required' => 'El correo electrónico es obligatorio.',
        'email.string' => 'El correo electrónico debe ser una cadena de texto.',
        'email.max' => 'El correo electrónico no puede tener más de 100 caracteres.',
        'email.email' => 'El correo electrónico debe ser válido.',
        'email.unique' => 'El correo electrónico ya está registrado.',

        'password.required' => 'La contraseña es obligatoria.',
        'password.string' => 'La contraseña debe ser una cadena de texto.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

        'celular.required' => 'El número de celular es obligatorio.',
        'celular.digits' => 'El número de celular debe tener exactamente 10 dígitos.',
        'celular.regex' => 'El número de celular solo puede contener números.',
        'celular.unique' => 'El número de celular ya está registrado.',
    ];

    $validator = Validator::make($data, $rules, $messages);

    if ($validator->fails()) {
        $this->error('No se pudo crear el administrador debido a errores en los datos ingresados:');
        foreach ($validator->errors()->all() as $message) {
            $this->line("- $message");
        }
        return Command::FAILURE;
    }

    // Crear el usuario administrador
    User::create([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'celular' => $celular,
        'email' => $email,
        'password' => Hash::make($password),
        'esadmin' => true,
    ]);

    $this->info('Administrador creado exitosamente.');
    return Command::SUCCESS;
    
}
}
