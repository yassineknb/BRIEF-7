<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Liste des types d'exceptions qui ne sont pas reportées
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Liste des inputs qui ne sont jamais flashés en session
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Enregistrer les callbacks de gestion des exceptions
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Erreur quand une ressource n'est pas trouvée en base de données
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Ressource non trouvée'
                ], 404);
            }
        });

        // Erreur quand la route n'existe pas
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Route non trouvée'
                ], 404);
            }
        });

        // Erreur d'accès refusé (permissions)
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Accès refusé - Permissions insuffisantes'
                ], 403);
            }
        });

        // Erreur de validation des données
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Erreur de validation',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // Erreur d'authentification (token invalide ou manquant)
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Non authentifié - Token manquant ou invalide'
                ], 401);
            }
        });

        // Gestion des autres erreurs (erreur 500)
        $this->renderable(function (\Exception $e, $request) {
            // Afficher les détails uniquement en mode debug
            if ($request->is('api/*') && !config('app.debug')) {
                return response()->json([
                    'message' => 'Une erreur est survenue sur le serveur'
                ], 500);
            }
        });
    }
}
