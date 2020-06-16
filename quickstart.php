<? php
requiere __DIR__ .  '/vendor/autoload.php' ;

if ( php_sapi_name ()! = 'cli' ) {    
    Lanzar Una Nueva excepci ó n ( 'Esta Aplicación Dębe ejecutarse en La Línea de Comando' );
}

/ ** 
 * Devuelve un cliente API autorizado . 
 * @return Google_Client el objeto de cliente autorizado  
 * / 
función getClient ()
{
    $ cliente = nuevo Google_Client ();
    $ client-> setApplicationName ('Inicio rápido de PHP de Google Classroom API');
    $ client-> setScopes (Google_Service_Classroom :: CLASSROOM_COURSES_READONLY);
    $ client-> setAuthConfig ('credentials.json');
    $ client-> setAccessType ('fuera de línea');
    $ client-> setPrompt ('select_account consent');

    / / Cargar token previamente autorizado de un archivo , si existe . 
    // El archivo token.json almacena los tokens de acceso y actualización del usuario, sí
    // creado automáticamente cuando se completa el flujo de autorización para el primer
    // hora.
    $ tokenPath = 'token.json' ; 
    if ( file_exists ( $ tokenPath )) {  
        $ accessToken = json_decode ( file_get_contents ( $ tokenPath ), verdadero );
        $ cliente -> setAccessToken ( $ accessToken );
    }

    // Si no hay token anterior o está caducado.
    if ( $ client -> isAccessTokenExpired ()) {  
        // Actualiza el token si es posible, de lo contrario, busca uno nuevo.
        if ( $ client -> getRefreshToken ()) {  
            $ cliente -> fetchAccessTokenWithRefreshToken ( $ cliente -> getRefreshToken ());
        } m á s {
            // Solicitar autorización del usuario.
            $ authUrl = $ cliente -> createAuthUrl ();
            printf ( "Abra el siguiente enlace en su navegador: \ n% s \ n" , $ authUrl );
            imprimir 'Ingrese el código de verificación:' ; 
            $ authCode = trim ( fgets ( STDIN ));

            // Código de autorización de intercambio para un token de acceso.
            $ accessToken = $ client -> fetchAccessTokenWithAuthCode ( $ authCode );
            $ cliente -> setAccessToken ( $ accessToken );

            // Verifique si hubo un error.
            if ( array_key_exists ( 'error' , $ accessToken )) {  
                Lanzar Una Nueva excepci ó n ( unirse a ( '' , $ accessToken ));
            }
        }
        // Guardar el token en un archivo.
        if (! file_exists ( dirname ( $ tokenPath ))) {  
            mkdir ( dirname ( $ tokenPath ), 0700 , verdadero ); 
        }
        file_put_contents ( $ tokenPath , json_encode ( $ client -> getAccessToken ()));
    }
    devolver $ cliente ;
}


// Obtenga el cliente API y construya el objeto de servicio.
$ cliente = getClient ();
$ servicio = nuevo Google_Service_Classroom ( $ cliente ); 

// Imprime los primeros 10 cursos a los que el usuario tiene acceso.
$ optParams = array (
  'pageSize' => 10  
);
$ resultados = $ servicio -> cursos -> listCourses ( $ optParams );

if ( cuenta ( $ resultados -> getCourses ()) == 0 ) {    
  imprima "No se encontraron cursos. \ n" ; 
} m á s {
  imprimir "Cursos: \ n" ;
  foreach ( $ resultados -> getCourses () como $ course ) {  
    printf ( "% s (% s) \ n" , $ curso -> getName (), $ curso -> getId ());
  }
}
