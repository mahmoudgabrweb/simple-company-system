<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin', "middleware" => ['company.context']], function () {
    Route::get('/admin/company/select', [\App\Http\Controllers\Admin\CompanySwitchController::class, 'select'])
        ->name('admin.company.select')->middleware(['web', 'auth']);
    Route::post('/admin/company/select', [\App\Http\Controllers\Admin\CompanySwitchController::class, 'store'])
        ->name('admin.company.set')->middleware(['web', 'auth']);

    Route::name('voyager.')->group(function () {
        Route::get('roles-index', [RoleController::class, 'index'])->name('roles.index-new');
        Route::get('roles/create-new', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::name('voyager.')->group(function () {
        Route::get('users-index', [UserController::class, 'index'])->name('users.index-new');           // or users/index-new
        Route::get('users/create-new', [UserController::class, 'create'])->name('users.create-new');         // or users/create-new
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->whereNumber('user');
    });

    Route::get('jobs/load', [\App\Http\Controllers\Admin\JobController::class, 'load'])->name('voyager.jobs.load');
    Route::post('jobs/{id}/toggle', [\App\Http\Controllers\Admin\JobController::class, 'toggle'])->name('voyager.jobs.toggle');

    Route::get('employees/load', [\App\Http\Controllers\Admin\EmployeeController::class, 'load'])->name('voyager.employees.load');
    Route::post('employees/{id}/toggle', [\App\Http\Controllers\Admin\EmployeeController::class, 'toggle'])->name('voyager.employees.toggle');

    Route::get('cities/load', [\App\Http\Controllers\Admin\CityController::class, 'load'])->name('voyager.cities.load');

    Route::get('clients/load', [\App\Http\Controllers\Admin\ClientController::class, 'load'])->name('voyager.clients.load');

    Route::get('projects/load', [\App\Http\Controllers\Admin\ProjectController::class, 'load'])->name('voyager.projects.load');

    Route::get('expense_types/load', [\App\Http\Controllers\Admin\ExpenseTypeController::class, 'load'])->name('voyager.expense_types.load');

    Route::get('expenses/load', [\App\Http\Controllers\Admin\ExpenseController::class, 'load'])->name('voyager.expenses.load');

    Route::get('salaries/load', [\App\Http\Controllers\Admin\SalaryController::class, 'load'])->name('voyager.salaries.load');

    // send/resend + status
    Route::post('quotations/{id}/send', [\App\Http\Controllers\Admin\QuotationController::class, 'send'])->name('admin.quotations.send');
    Route::post('quotations/{id}/resend', [\App\Http\Controllers\Admin\QuotationController::class, 'resend'])->name('admin.quotations.resend');
    Route::post('quotations/{id}/status', [\App\Http\Controllers\Admin\QuotationController::class, 'changeStatus'])->name('admin.quotations.status');

    // sections
    Route::post('quotations/{id}/sections', [\App\Http\Controllers\Admin\QuotationController::class, 'addSection'])->name('admin.quotations.sections.add');
    Route::put('quotation-sections/{section}', [\App\Http\Controllers\Admin\QuotationController::class, 'updateSection'])->name('admin.quotations.sections.update');
    Route::delete('quotation-sections/{section}', [\App\Http\Controllers\Admin\QuotationController::class, 'deleteSection'])->name('admin.quotations.sections.delete');
    Route::post('quotations/{id}/sections/reorder', [\App\Http\Controllers\Admin\QuotationController::class, 'reorderSections'])->name('admin.quotations.sections.reorder');

    // items
    Route::post('quotation-sections/{section}/items', [\App\Http\Controllers\Admin\QuotationController::class, 'addItem'])->name('admin.quotations.items.add');
    Route::put('quotation-items/{item}', [\App\Http\Controllers\Admin\QuotationController::class, 'updateItem'])->name('admin.quotations.items.update');
    Route::delete('quotation-items/{item}', [\App\Http\Controllers\Admin\QuotationController::class, 'deleteItem'])->name('admin.quotations.items.delete');
    Route::post('quotation-sections/{section}/items/reorder', [\App\Http\Controllers\Admin\QuotationController::class, 'reorderItems'])->name('admin.quotations.items.reorder');

    Route::get('payments/{id}/attachment', [\App\Http\Controllers\Admin\PaymentController::class, 'download'])
        ->name('admin.payments.attachment');

    Route::get('project-expenses/{id}/attachment', [\App\Http\Controllers\Admin\ProjectExpenseController::class, 'download'])
        ->name('admin.project-expenses.attachment');

    Voyager::routes();
});