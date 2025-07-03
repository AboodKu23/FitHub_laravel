<?php
use App\Http\Controllers\Auth\AuthenticatedController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterStepOneController;
use App\Http\Controllers\Services\ExercisesController;
use App\Http\Controllers\Services\PostController;
use App\Http\Controllers\Trainee\RegisterTraineeController;
use App\Http\Controllers\Trainee\TraineeSubscriptionController;
use App\Http\Controllers\Trainee\TrainersIntegrationController;
use App\Http\Controllers\Trainer\CustomizedTrainingExerciseController;
use App\Http\Controllers\Trainer\ManagePostController;
use App\Http\Controllers\Trainer\RegisterTrainerController;
use App\Http\Controllers\Trainer\SubscriptionPlanController;
use App\Http\Controllers\Trainer\TraineeIntegrationController;
use App\Http\Controllers\Trainer\TrainerSubscriptionController;
use App\Http\Controllers\Trainer\TrainingPlanController;
use App\Http\Controllers\Trainer\TrainingPlanExerciseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('Auth')->group(function () {
    Route::post('/register',[RegisterStepOneController::class,'firstStepRegister']); //Done
    Route::post('/trainee/register', [RegisterTraineeController::class,'register']); //Done
    Route::post('/trainer/register', [RegisterTrainerController::class,'register']);
    Route::post('/trainer/certificate', [RegisterTrainerController::class,'addCertificate']); //Done
    Route::post('/login', [AuthenticatedController::class, 'login']); //Done

    Route::prefix('Email')->group(function () {
        Route::post('/verify-code', [EmailVerificationController::class, 'verify'])
            ->name('verification.verify.code'); //Done
        Route::post('/resend', [EmailVerificationController::class, 'resend'])
            ->middleware('auth:sanctum');
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
   Route::prefix('Services')->group(function () {
       Route::prefix('Exercises')->group(function () {
           Route::get('/get-exercises',[ExercisesController::class,'getAllExercises']); //Done
           Route::get('/get-selected-exercise/{id}',[ExercisesController::class,'getSelectedExercise']); //Done
       }); //Done

       Route::prefix('Community')->group(function () {
           Route::post('/{id}/like-post',[PostController::class,'likePost']); //Done
           Route::post('/{id}/unLike-post',[PostController::class,'unlikePost']); //Done
           Route::post('/{id}/dislike-post',[PostController::class,'dislikePost']); //Done
           Route::post('/{id}/unDislike-post',[PostController::class,'unDislikePost']); //Done
           Route::get('/get-posts', [PostController::class,'getAllPosts']); //Done
       }); //Done


   }) ;
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('Trainee')->group(function () {
        Route::prefix('trainers_integration')->group(function () {
            Route::get('trainers', [TrainersIntegrationController::class, 'getTrainers']); //Done
            Route::get('trainer/{id}', [TrainersIntegrationController::class, 'getTrainer']); //Done
        }); //Done

        Route::prefix('subscription_requests')->group(function () {
            Route::post('/send/{id}', [TraineeSubscriptionController::class, 'sendSubscriptionRequest']); //Done
            Route::get('/get-requests', [TraineeSubscriptionController::class, 'getTraineeSubscriptionRequests']); //Done
            Route::get('/get-accepted-requests', [TraineeSubscriptionController::class, 'getTraineeAcceptedSubscriptionRequests']);
            Route::get('/get-rejected-requests', [TraineeSubscriptionController::class, 'getTraineeRejectedSubscriptionRequests']);
            Route::get('/get-cancelled-requests', [TraineeSubscriptionController::class, 'getTrainerCancelledSubscriptionRequests']);
            Route::get('/get-active-requests', [TraineeSubscriptionController::class, 'getActiveSubscriptionRequests']);
            Route::post('/cancel/{id}', [TraineeSubscriptionController::class, 'cancelSubscriptionRequest']); //Done
        });
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('Trainer')->group(function () {
        Route::prefix('subscription_requests')->group(function () {
            Route::get('/get-requests', [TrainerSubscriptionController::class, 'getActiveRequest']); //Done
            Route::post('/accept/{id}', [TrainerSubscriptionController::class, 'acceptRequest']); //Done
            Route::post('/reject/{id}', [TrainerSubscriptionController::class, 'rejectRequest']); //Done
            Route::get('/get-accepted-requests', [TrainerSubscriptionController::class, 'getApprovedRequests']);
            Route::get('/get-rejected-requests', [TrainerSubscriptionController::class, 'getRejectedRequests']);
        });

        Route::prefix('subscriptions')->group(function () {
            Route::get('/get-active-subscriptions', [TrainerSubscriptionController::class, 'getActiveSubscriptions']);
            Route::get('/get-subscriptions/{id}', [TrainerSubscriptionController::class, 'getSubscription']);
            Route::get('/get-trainee/{id}', [TraineeIntegrationController::class, 'getTraineeInfo']);

            Route::prefix('plan')->group(function () {
               Route::post('/assign-plan/{id}', [SubscriptionPlanController::class, 'assignPlan']); //Done
               Route::post('/unassign-plan/{id}', [SubscriptionPlanController::class, 'unassignPlan']); //Done
               Route::get('/get-subscription-plan/{id}', [SubscriptionPlanController::class, 'getSubscriptionPlan']); //Done
               Route::get('/get-subscription-plan-exercise/{id}', [SubscriptionPlanController::class, 'getSubscriptionPlanExercises']); //Done
               Route::post('/customize-plan/{id}', [CustomizedTrainingExerciseController::class, 'customizeTrainingExercises']); //Done
            }); //Done UnderB
        });

        Route::prefix('Plans')->group(function () {
            Route::post('/create-plan', [TrainingPlanController::class, 'createNewTrainingPlan']); //Done
            Route::post('delete-plan/{id}', [TrainingPlanController::class, 'deleteTrainingPlan']); //Done
            Route::get('/get-all-plans', [TrainingPlanController::class, 'getAllTrainingPlans']); //Done
            Route::get('/get-plan/{id}', [TrainingPlanController::class, 'getTrainingPlan']); //Done

            Route::prefix('Plan')->group(function () {
                Route::post('/save-plan/{id}', [TrainingPlanExerciseController::class, 'saveTrainingPlan']); //Done
                Route::post('/save-plan-exercise/{id}', [TrainingPlanExerciseController::class, 'addExerciseDetails']); //Done
                Route::post('/save-plan-exercise-order/{id}', [TrainingPlanExerciseController::class, 'updateExerciseDayOrder']); //Done
                Route::get('/plan-exercises/{id}', [TrainingPlanExerciseController::class, 'getPlanExercises']); //Done
                Route::get('/plan-exercise/{id}', [TrainingPlanExerciseController::class, 'getPlanExerciseById']); //Done
                Route::get('/day-exercises/{id}',[TrainingPlanExerciseController::class,'getDayExercises'] ); //Done
            });
        }); //Done

        Route::prefix('Posts')->group(function () {
            Route::post('/create-post', [ManagePostController::class, 'createPost']); //Done
            Route::post('/{id}/delete-post', [ManagePostController::class, 'deleteTrainerPost']); //Done
            Route::post('/{id}/update-post', [ManagePostController::class, 'UpdateTrainerPost']); //Done
            Route::get('/get-trainer-posts', [ManagePostController::class, 'getTrainerPosts']); //Done
        }); //Done
    });
});
