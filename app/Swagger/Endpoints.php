<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * L5-Swagger endpoint annotations.
 *
 * Controllers stay focused on HTTP behaviour; this class is the single source
 * for operation metadata and references the shared schemas in Schemas.php.
 *
 * @OA\Get(path="/api/auth/google/redirect", operationId="google.redirect", tags={"Auth"}, security={}, @OA\Parameter(name="intent", in="query", description="SSO entry point", @OA\Schema(type="string", enum={"login","register"}, default="login")), @OA\Response(response=302, description="Redirect to Google"))
 * @OA\Get(path="/api/auth/google/callback", operationId="google.callback", tags={"Auth"}, security={}, description="Authenticates an existing account by Google email. Unknown users are not created and are redirected to registration.", @OA\Parameter(name="code", in="query", @OA\Schema(type="string")), @OA\Parameter(name="state", in="query", @OA\Schema(type="string")), @OA\Response(response=302, description="Dashboard or registration redirect"))
 *
 * @OA\Post(path="/api/login", operationId="auth.login", tags={"Auth"}, security={}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/LoginRequest")), @OA\Response(response=200, description="Authenticated", @OA\JsonContent(ref="#/components/schemas/AuthResponse")), @OA\Response(response=422, ref="#/components/responses/ValidationError"))
 * @OA\Post(path="/api/register", operationId="auth.register", tags={"Auth"}, security={}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/RegisterRequest")), @OA\Response(response=201, description="Registered", @OA\JsonContent(ref="#/components/schemas/AuthResponse")), @OA\Response(response=422, ref="#/components/responses/ValidationError"))
 * @OA\Post(path="/api/logout", operationId="auth.logout", tags={"Auth"}, security={{"sanctum":{}}}, @OA\Response(response=200, description="Logged out"), @OA\Response(response=401, ref="#/components/responses/Unauthenticated"))
 *
 * @OA\Get(path="/api/me", operationId="auth.me", tags={"Auth"}, security={{"sanctum":{}}}, @OA\Response(response=200, description="Current user", @OA\JsonContent(ref="#/components/schemas/User")), @OA\Response(response=401, ref="#/components/responses/Unauthenticated"))
 * @OA\Get(path="/api/users", operationId="users.index", tags={"Users"}, security={{"sanctum":{}}}, @OA\Response(response=200, description="Users"), @OA\Response(response=403, ref="#/components/responses/Forbidden"))
 * @OA\Get(path="/api/users/{id}", operationId="users.show", tags={"Users"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="User", @OA\JsonContent(ref="#/components/schemas/User")), @OA\Response(response=404, ref="#/components/responses/NotFound"))
 *
 * @OA\Post(path="/api/users/{id}", operationId="users.updatePost", tags={"Users"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/UserUpdateRequest"))), @OA\Response(response=200, description="Updated"), @OA\Response(response=422, ref="#/components/responses/ValidationError"))
 *
 * @OA\Put(path="/api/users/{id}", operationId="users.updatePut", tags={"Users"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/UserUpdateRequest"))), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Patch(path="/api/users/{id}", operationId="users.updatePatch", tags={"Users"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/UserUpdateRequest"))), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Get(path="/api/service-categories", operationId="categories.index", tags={"Service categories"}, security={}, @OA\Response(response=200, description="Categories"))
 *
 * @OA\Post(path="/api/service-categories", operationId="categories.store", tags={"Service categories"}, security={}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ServiceCategoryRequest")), @OA\Response(response=201, description="Created"), @OA\Response(response=422, ref="#/components/responses/ValidationError"))
 *
 * @OA\Get(path="/api/service-categories/{id}", operationId="categories.show", tags={"Service categories"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Category"))
 *
 * @OA\Put(path="/api/service-categories/{id}", operationId="categories.updatePut", tags={"Service categories"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ServiceCategoryRequest")), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Patch(path="/api/service-categories/{id}", operationId="categories.updatePatch", tags={"Service categories"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ServiceCategoryRequest")), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Delete(path="/api/service-categories/{id}", operationId="categories.destroy", tags={"Service categories"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=204, description="Deleted"))
 *
 * @OA\Get(path="/api/services", operationId="services.index", tags={"Services"}, security={}, @OA\Response(response=200, description="Services"))
 *
 * @OA\Post(path="/api/services", operationId="services.store", tags={"Services"}, security={{"sanctum":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ServiceRequest")), @OA\Response(response=201, description="Created"))
 *
 * @OA\Get(path="/api/services-search", operationId="services.search", tags={"Services"}, security={}, @OA\Parameter(name="query", in="query", @OA\Schema(type="string")), @OA\Response(response=200, description="Search results"))
 * @OA\Get(path="/api/services/{slug}", operationId="services.show", tags={"Services"}, security={}, @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Service"))
 *
 * @OA\Put(path="/api/services/{id}", operationId="services.updatePut", tags={"Services"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ServiceRequest")), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Patch(path="/api/services/{id}", operationId="services.updatePatch", tags={"Services"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ServiceRequest")), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Delete(path="/api/services/{id}", operationId="services.destroy", tags={"Services"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=204, description="Deleted"))
 *
 * @OA\Get(path="/api/missions", operationId="missions.index", tags={"Missions"}, security={{"sanctum":{}}}, @OA\Response(response=200, description="Missions"))
 *
 * @OA\Post(path="/api/missions", operationId="missions.store", tags={"Missions"}, security={{"sanctum":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/MissionRequest")), @OA\Response(response=201, description="Created"))
 *
 * @OA\Get(path="/api/missions/{slug}", operationId="missions.show", tags={"Missions"}, security={{"sanctum":{}}}, @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Mission"))
 *
 * @OA\Put(path="/api/missions/{id}", operationId="missions.updatePut", tags={"Missions"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/MissionUpdateRequest")), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Patch(path="/api/missions/{id}", operationId="missions.updatePatch", tags={"Missions"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/MissionUpdateRequest")), @OA\Response(response=200, description="Updated"))
 *
 * @OA\Delete(path="/api/missions/{id}", operationId="missions.destroy", tags={"Missions"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=204, description="Deleted"))
 *
 * @OA\Post(path="/api/missions/{id}/claim", operationId="missions.claim", tags={"Missions"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Claimed"), @OA\Response(response=409, description="Schedule conflict"))
 * @OA\Post(path="/api/missions/generate-with-ai", operationId="missions.generateAi", tags={"AI"}, security={{"sanctum":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/MissionAiRequest")), @OA\Response(response=200, description="Generated mission"), @OA\Response(response=429, description="Rate limited"))
 *
 * @OA\Get(path="/api/messages", operationId="messages.index", tags={"Messages"}, security={{"sanctum":{}}}, @OA\Response(response=200, description="Conversations"))
 * @OA\Get(path="/api/messages/conversation/{user}", operationId="messages.conversation", tags={"Messages"}, security={{"sanctum":{}}}, @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")), @OA\Parameter(name="mission_id", in="query", @OA\Schema(type="integer", nullable=true)), @OA\Response(response=200, description="Conversation"))
 *
 * @OA\Post(path="/api/messages", operationId="messages.store", tags={"Messages"}, security={{"sanctum":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/MessageRequest")), @OA\Response(response=201, description="Sent"), @OA\Response(response=422, ref="#/components/responses/ValidationError"))
 * @OA\Post(path="/api/reviews", operationId="reviews.store", tags={"Reviews"}, security={{"sanctum":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ReviewRequest")), @OA\Response(response=201, description="Published"))
 *
 * @OA\Patch(path="/api/reviews/{id}", operationId="reviews.update", tags={"Reviews"}, security={{"sanctum":{}}}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ReviewUpdateRequest")), @OA\Response(response=200, description="Final revision"))
 *
 * @OA\Post(path="/api/ai/translate", operationId="ai.translate", tags={"AI"}, security={{"sanctum":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/TranslationRequest")), @OA\Response(response=200, description="Translation"))
 *
 * @OA\Get(path="/api/user-skills", operationId="skills.index", tags={"User skills"}, security={}, @OA\Response(response=200, description="Skills"))
 *
 * @OA\Post(path="/api/user-skills", operationId="skills.store", tags={"User skills"}, security={}, @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UserSkillRequest")), @OA\Response(response=201, description="Created"))
 *
 * @OA\Get(path="/api/user-skills/{id}", operationId="skills.show", tags={"User skills"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Skill"))
 *
 * @OA\Put(path="/api/user-skills/{id}", operationId="skills.updatePut", tags={"User skills"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Patch(path="/api/user-skills/{id}", operationId="skills.updatePatch", tags={"User skills"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Delete(path="/api/user-skills/{id}", operationId="skills.destroy", tags={"User skills"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=204, description="Deleted"))
 *
 * @OA\Get(path="/api/resumes", operationId="resumes.index", tags={"Resumes"}, security={}, @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Post(path="/api/resumes", operationId="resumes.store", tags={"Resumes"}, security={}, @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Get(path="/api/resumes/{id}", operationId="resumes.show", tags={"Resumes"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Put(path="/api/resumes/{id}", operationId="resumes.updatePut", tags={"Resumes"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Patch(path="/api/resumes/{id}", operationId="resumes.updatePatch", tags={"Resumes"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Delete(path="/api/resumes/{id}", operationId="resumes.destroy", tags={"Resumes"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Get(path="/api/portfolio-items", operationId="portfolio.index", tags={"Portfolio"}, security={}, @OA\Response(response=200, description="Portfolio items"))
 *
 * @OA\Post(path="/api/portfolio-items", operationId="portfolio.store", tags={"Portfolio"}, security={}, @OA\Response(response=201, description="Created"))
 *
 * @OA\Get(path="/api/portfolio-items/{id}", operationId="portfolio.show", tags={"Portfolio"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Portfolio item"))
 *
 * @OA\Put(path="/api/portfolio-items/{id}", operationId="portfolio.updatePut", tags={"Portfolio"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Patch(path="/api/portfolio-items/{id}", operationId="portfolio.updatePatch", tags={"Portfolio"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Not implemented"))
 *
 * @OA\Delete(path="/api/portfolio-items/{id}", operationId="portfolio.destroy", tags={"Portfolio"}, security={}, @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=204, description="Deleted"))
 *
 * @OA\Post(path="/api/missions/{id}/payments", operationId="payments.store", tags={"Payments"}, security={{"sanctum":{}}}, description="Initialise un paiement pour une mission en cours appartenant au client authentifié. Une à cinq photos de réalisation sont obligatoires.", @OA\Parameter(ref="#/components/parameters/Id"), @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/PaymentCreateRequest"))), @OA\Response(response=201, description="Paiement initialisé", @OA\JsonContent(ref="#/components/schemas/PaymentResponse")), @OA\Response(response=403, ref="#/components/responses/Forbidden"), @OA\Response(response=409, description="Mission déjà payée"), @OA\Response(response=422, ref="#/components/responses/ValidationError"), @OA\Response(response=502, description="Passerelle de paiement indisponible"))
 * @OA\Get(path="/api/payments/{id}", operationId="payments.show", tags={"Payments"}, security={{"sanctum":{}}}, description="Retourne un paiement appartenant au client authentifié.", @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=200, description="Paiement", @OA\JsonContent(ref="#/components/schemas/PaymentResponse")), @OA\Response(response=403, ref="#/components/responses/Forbidden"))
 * @OA\Get(path="/api/payments/mobile/return/{id}", operationId="payments.mobileReturn", tags={"Payments"}, security={}, description="Capture si nécessaire le paiement PayPal puis redirige vers barasira://payments/{id}.", @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=302, description="Redirection vers l’application mobile"))
 * @OA\Get(path="/api/payments/mobile/cancel/{id}", operationId="payments.mobileCancel", tags={"Payments"}, security={}, description="Annule un paiement encore en attente puis redirige vers barasira://payments/{id}.", @OA\Parameter(ref="#/components/parameters/Id"), @OA\Response(response=302, description="Redirection vers l’application mobile"))
 *
 * @OA\Get(path="/api/payments/webhooks/cinetpay", operationId="payments.cinetpayPing", tags={"Payments"}, security={}, @OA\Response(response=204, description="Webhook availability"))
 *
 * @OA\Post(path="/api/payments/webhooks/cinetpay", operationId="payments.cinetpayWebhook", tags={"Payments"}, security={}, @OA\RequestBody(@OA\MediaType(mediaType="application/x-www-form-urlencoded", @OA\Schema(@OA\Property(property="cpm_trans_id", type="string")))), @OA\Response(response=204, description="Notification verified"))
 */
class Endpoints {}
