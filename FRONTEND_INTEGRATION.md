# 📱 Guía de Integración Frontend - Verificación OTP

Esta guía explica cómo integrar la verificación OTP con SendGrid en el frontend.

## 🔄 Flujo de Registro con OTP

### Paso 1: Registrar Usuario

**Endpoint:** `POST /api/register`

**Request:**
```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "pregunta_secreta": "¿Cuál es el nombre de tu primera mascota?",
  "respuesta_secreta": "Doki"
}
```

**Response (201):**
```json
{
  "message": "Registro exitoso. Ingresa el código enviado a tu correo para activar tu cuenta. El código expira en 10 minutos.",
  "email": "juan@example.com"
}
```

**⚠️ Importante:** 
- NO se devuelve token JWT en este paso
- El usuario NO puede hacer login hasta verificar el OTP
- El código OTP expira en 10 minutos

### Paso 2: Verificar Código OTP

**Endpoint:** `POST /api/otp/verify-activation`

**Request:**
```json
{
  "email": "juan@example.com",
  "code": "123456"
}
```

**Response Exitosa (200):**
```json
{
  "message": "Código verificado correctamente. Cuenta activada.",
  "token": "eyJ0eXAiOiJKV1QiLCJh...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": "507f1f77bcf86cd799439011",
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "email_verified_at": "2024-01-01 12:00:00"
  }
}
```

**Errores Posibles:**

- **400 - Código incorrecto:**
```json
{
  "message": "Código incorrecto. Verifica el código e intenta nuevamente."
}
```

- **400 - Código expirado:**
```json
{
  "message": "Código expirado. El código OTP solo es válido por 10 minutos. Solicita uno nuevo."
}
```

- **404 - Usuario no encontrado:**
```json
{
  "message": "Usuario no encontrado."
}
```

### Paso 3: Reenviar Código OTP (si es necesario)

**Endpoint:** `POST /api/otp/resend-activation`

**Request:**
```json
{
  "email": "juan@example.com"
}
```

**Response (200):**
```json
{
  "message": "Nuevo código enviado al correo. Recuerda que el código expira en 10 minutos."
}
```

**Errores:**

- **400 - Cuenta ya activada:**
```json
{
  "message": "Esta cuenta ya está activada."
}
```

## 🔐 Flujo de Login

**Endpoint:** `POST /api/login`

**Request:**
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Response Exitosa (200):**
```json
{
  "message": "Login exitoso",
  "token": "eyJ0eXAiOiJKV1QiLCJh...",
  "user": {
    "id": "507f1f77bcf86cd799439011",
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "google_id": null,
    "facebook_id": null
  }
}
```

**⚠️ Error si cuenta no está activada (403):**
```json
{
  "message": "Tu cuenta no está activada. Revisa tu correo para el código de verificación.",
  "email": "juan@example.com"
}
```

**Acción recomendada:** Redirigir al usuario a la pantalla de verificación OTP.

## 🔑 Recuperación de Contraseña con OTP

### Opción 1: Método OTP (Recomendado)

#### Paso 1: Solicitar Código OTP

**Endpoint:** `POST /api/password/verify-email`

**Request:**
```json
{
  "email": "juan@example.com",
  "method": "otp"
}
```

**Response (200):**
```json
{
  "message": "Código enviado al correo. Expira en 10 minutos.",
  "method": "otp"
}
```

#### Paso 2: Verificar Código OTP

**Endpoint:** `POST /api/otp/verify-password-recovery`

**Request:**
```json
{
  "email": "juan@example.com",
  "code": "123456"
}
```

**Response Exitosa (200):**
```json
{
  "message": "Código verificado correctamente. Ahora puedes cambiar tu contraseña."
}
```

#### Paso 3: Actualizar Contraseña

**Endpoint:** `POST /api/password/update`

**Request:**
```json
{
  "email": "juan@example.com",
  "new_password": "nuevaPassword123",
  "new_password_confirmation": "nuevaPassword123",
  "method": "otp",
  "otp_code": "123456"
}
```

**Response (200):**
```json
{
  "message": "Contraseña actualizada exitosamente."
}
```

### Opción 2: Método Pregunta Secreta (Alternativo)

#### Paso 1: Obtener Pregunta Secreta

**Endpoint:** `POST /api/password/verify-email`

**Request:**
```json
{
  "email": "juan@example.com",
  "method": "pregunta"
}
```

**Response (200):**
```json
{
  "email": "juan@example.com",
  "pregunta_secreta": "¿Cuál es el nombre de tu primera mascota?",
  "method": "pregunta"
}
```

#### Paso 2: Verificar Respuesta

**Endpoint:** `POST /api/password/verify-answer`

**Request:**
```json
{
  "email": "juan@example.com",
  "respuesta_secreta": "Doki"
}
```

#### Paso 3: Actualizar Contraseña

**Endpoint:** `POST /api/password/update`

**Request:**
```json
{
  "email": "juan@example.com",
  "new_password": "nuevaPassword123",
  "new_password_confirmation": "nuevaPassword123",
  "method": "pregunta",
  "respuesta_secreta": "Doki"
}
```

## 📋 Ejemplo de Implementación Frontend (Vue.js/React)

### Componente de Registro

```javascript
// RegistroComponent.vue o RegistroComponent.jsx

async function handleRegister(formData) {
  try {
    // Paso 1: Registrar usuario
    const registerResponse = await api.post('/api/register', {
      name: formData.name,
      email: formData.email,
      password: formData.password,
      password_confirmation: formData.passwordConfirmation,
      pregunta_secreta: formData.secretQuestion,
      respuesta_secreta: formData.secretAnswer
    });

    if (registerResponse.status === 201) {
      // Guardar email en estado/localStorage para el siguiente paso
      setEmail(formData.email);
      setShowOTPVerification(true);
      showMessage('Código enviado a tu correo. Verifica tu email.');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      // Errores de validación
      setErrors(error.response.data.errors);
    } else {
      showError('Error al registrar usuario. Intenta de nuevo.');
    }
  }
}
```

### Componente de Verificación OTP

```javascript
// OTPVerificationComponent.vue o OTPVerificationComponent.jsx

async function handleVerifyOTP(code) {
  try {
    const response = await api.post('/api/otp/verify-activation', {
      email: email, // Obtenido del paso anterior
      code: code
    });

    if (response.status === 200) {
      // Guardar token JWT
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      
      // Redirigir al dashboard
      router.push('/dashboard');
    }
  } catch (error) {
    if (error.response?.status === 400) {
      if (error.response.data.message.includes('expirado')) {
        showError('Código expirado. Solicita uno nuevo.');
        setShowResendButton(true);
      } else {
        showError('Código incorrecto. Intenta de nuevo.');
      }
    } else {
      showError('Error al verificar código. Intenta de nuevo.');
    }
  }
}

async function handleResendOTP() {
  try {
    const response = await api.post('/api/otp/resend-activation', {
      email: email
    });

    if (response.status === 200) {
      showMessage('Nuevo código enviado a tu correo.');
      setShowResendButton(false);
    }
  } catch (error) {
    if (error.response?.status === 400 && 
        error.response.data.message.includes('ya está activada')) {
      // La cuenta ya está activada, redirigir a login
      router.push('/login');
    } else {
      showError('Error al reenviar código. Intenta de nuevo.');
    }
  }
}
```

### Componente de Login (con verificación de cuenta activada)

```javascript
// LoginComponent.vue o LoginComponent.jsx

async function handleLogin(email, password) {
  try {
    const response = await api.post('/api/login', {
      email: email,
      password: password
    });

    if (response.status === 200) {
      // Guardar token y usuario
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      
      // Redirigir al dashboard
      router.push('/dashboard');
    }
  } catch (error) {
    if (error.response?.status === 403) {
      // Cuenta no activada
      showWarning('Tu cuenta no está activada.');
      // Redirigir a verificación OTP
      router.push({
        path: '/verify-otp',
        query: { email: email }
      });
    } else if (error.response?.status === 401) {
      showError('Credenciales inválidas.');
    } else {
      showError('Error al iniciar sesión. Intenta de nuevo.');
    }
  }
}
```

### Componente de Recuperación de Contraseña con OTP

```javascript
// PasswordRecoveryOTPComponent.vue o PasswordRecoveryOTPComponent.jsx

// Paso 1: Solicitar código OTP
async function requestOTP(email) {
  try {
    const response = await api.post('/api/password/verify-email', {
      email: email,
      method: 'otp'
    });

    if (response.status === 200) {
      setEmail(email);
      setShowOTPInput(true);
      showMessage('Código enviado a tu correo.');
    }
  } catch (error) {
    showError('Error al enviar código. Verifica tu email.');
  }
}

// Paso 2: Verificar código OTP
async function verifyOTP(code) {
  try {
    const response = await api.post('/api/otp/verify-password-recovery', {
      email: email,
      code: code
    });

    if (response.status === 200) {
      setOTPVerified(true);
      setShowPasswordForm(true);
    }
  } catch (error) {
    if (error.response?.status === 400) {
      if (error.response.data.message.includes('expirado')) {
        showError('Código expirado. Solicita uno nuevo.');
      } else {
        showError('Código incorrecto.');
      }
    }
  }
}

// Paso 3: Actualizar contraseña
async function updatePassword(newPassword, passwordConfirmation, otpCode) {
  try {
    const response = await api.post('/api/password/update', {
      email: email,
      new_password: newPassword,
      new_password_confirmation: passwordConfirmation,
      method: 'otp',
      otp_code: otpCode
    });

    if (response.status === 200) {
      showSuccess('Contraseña actualizada exitosamente.');
      router.push('/login');
    }
  } catch (error) {
    if (error.response?.status === 400) {
      showError('Código incorrecto o expirado. Solicita uno nuevo.');
    } else {
      showError('Error al actualizar contraseña.');
    }
  }
}
```

## 🎨 Recomendaciones de UX

### Pantalla de Registro
1. Formulario de registro normal
2. Después de registrar, mostrar mensaje: "Código enviado a tu correo"
3. Redirigir automáticamente a pantalla de verificación OTP

### Pantalla de Verificación OTP
1. Campo de entrada para código de 6 dígitos
2. Botón "Verificar"
3. Botón "Reenviar código" (deshabilitado por 60 segundos después de enviar)
4. Contador de tiempo restante (10 minutos)
5. Mensaje claro: "Ingresa el código de 6 dígitos enviado a tu correo"

### Pantalla de Login
1. Si el login falla con error 403 (cuenta no activada), mostrar:
   - Mensaje: "Tu cuenta no está activada"
   - Botón: "Reenviar código de verificación"
   - O redirigir automáticamente a verificación OTP

### Pantalla de Recuperación de Contraseña
1. Opción para elegir método: "Pregunta secreta" o "Código por email"
2. Si elige OTP:
   - Campo de email
   - Botón "Enviar código"
   - Campo de código OTP (aparece después de enviar)
   - Botón "Verificar código"
   - Formulario de nueva contraseña (aparece después de verificar)

## ⚠️ Manejo de Errores

### Códigos de Estado HTTP

- **200**: Operación exitosa
- **201**: Recurso creado exitosamente
- **400**: Error de validación o código incorrecto/expirado
- **401**: No autenticado (credenciales inválidas)
- **403**: Cuenta no activada
- **404**: Recurso no encontrado
- **422**: Error de validación de datos
- **500**: Error interno del servidor

### Mensajes de Error Comunes

```javascript
const errorMessages = {
  'Código incorrecto': 'El código ingresado no es correcto. Verifica e intenta de nuevo.',
  'Código expirado': 'El código ha expirado. Solicita uno nuevo.',
  'No hay código activo': 'No hay código activo. Solicita uno nuevo.',
  'Cuenta no activada': 'Tu cuenta no está activada. Verifica tu correo.',
  'Esta cuenta ya está activada': 'Esta cuenta ya está activada. Puedes iniciar sesión.',
  'Usuario no encontrado': 'No se encontró un usuario con ese correo.',
  'Credenciales inválidas': 'El correo o contraseña son incorrectos.',
  'No se pudo enviar el correo': 'Error al enviar el correo. Intenta de nuevo más tarde.'
};
```

## 🔒 Seguridad

1. **No almacenar códigos OTP en localStorage o sessionStorage**
2. **Limpiar el código OTP del estado después de verificar**
3. **Implementar rate limiting en el frontend** (máximo 3 intentos de verificación)
4. **Mostrar contador de tiempo restante** para códigos OTP
5. **Deshabilitar botón de reenvío** por 60 segundos después de cada envío

## 📝 Notas Importantes

- Los códigos OTP expiran en **10 minutos**
- Los códigos OTP son de **6 dígitos**
- El token JWT solo se devuelve después de verificar el OTP de activación
- El login fallará si la cuenta no está activada (error 403)
- El método de recuperación por OTP es independiente del método por pregunta secreta

