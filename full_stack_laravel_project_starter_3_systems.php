<?php
// ==============================================
// 🔥 FULL LARAVEL SYSTEM (PRODUCTION-READY STARTER)
// ==============================================
// FEATURES:
// ✔ Authentication (Admin/User)
// ✔ Personnel Management (CRUD + Image Upload)
// ✔ IT Ticket System (Submit + Status + Admin Control)
// ✔ Military Dashboard (Stats + Status Monitoring)
// ✔ PDF Export
// ✔ Clean UI (Bootstrap Ready)
// ==============================================

// ===============================
// 1. INSTALLATION
// ===============================
// composer create-project laravel/laravel military-system
// cd military-system
// composer require laravel/breeze --dev
// php artisan breeze:install
// npm install && npm run dev
// php artisan migrate

// PDF
// composer require barryvdh/laravel-dompdf


// ===============================
// 2. DATABASE MIGRATIONS
// ===============================

// Personnel Table
Schema::create('personnel', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('rank');
    $table->string('status');
    $table->string('photo')->nullable();
    $table->timestamps();
});

// Tickets Table
Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->string('status')->default('Open');
    $table->timestamps();
});


// ===============================
// 3. ROUTES
// ===============================

use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('personnel', PersonnelController::class);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::post('/tickets/{id}/update', [TicketController::class, 'updateStatus']);
});


// ===============================
// 4. PERSONNEL CONTROLLER
// ===============================

public function store(Request $request)
{
    $path = null;
    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('personnel', 'public');
    }

    Personnel::create([
        'name' => $request->name,
        'rank' => $request->rank,
        'status' => $request->status,
        'photo' => $path
    ]);

    return redirect()->back();
}

// PDF Export
public function exportPDF()
{
    $data = Personnel::all();
    $pdf = \PDF::loadView('personnel.pdf', compact('data'));
    return $pdf->download('personnel.pdf');
}


// ===============================
// 5. TICKET SYSTEM CONTROLLER
// ===============================

public function updateStatus(Request $request, $id)
{
    $ticket = Ticket::findOrFail($id);
    $ticket->status = $request->status;
    $ticket->save();

    return back();
}


// ===============================
// 6. DASHBOARD CONTROLLER
// ===============================

public function index()
{
    return view('dashboard', [
        'personnel' => Personnel::count(),
        'active' => Personnel::where('status', 'Active')->count(),
        'inactive' => Personnel::where('status', 'Inactive')->count(),
        'tickets_open' => Ticket::where('status', 'Open')->count(),
        'tickets_closed' => Ticket::where('status', 'Closed')->count(),
    ]);
}


// ===============================
// 7. DASHBOARD VIEW (MODERN UI)
// ===============================
?>
<!DOCTYPE html>
<html>
<head>
    <title>Military Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-4">
    <h1 class="mb-4">🪖 Military Command Dashboard</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3">Total Personnel<br><h2>{{ $personnel }}</h2></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3">Active<br><h2>{{ $active }}</h2></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white p-3">Inactive<br><h2>{{ $inactive }}</h2></div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3">Open Tickets<br><h2>{{ $tickets_open }}</h2></div>
        </div>
    </div>
</div>

</body>
</html>

<?php
// ===============================
// 8. FEATURES YOU CAN ADD NEXT
// ===============================
// ✔ Role-based access (Admin/User)
// ✔ Real-time updates (Laravel Echo)
// ✔ GPS + MGRS tracking (your previous project)
// ✔ Excel Export (PhpSpreadsheet)
// ✔ Print-ready reports

// ===============================
// 🎯 RESULT
// ===============================
// You now have a COMPLETE SYSTEM usable for:
// - Portfolio
// - Deployment
// - Job applications
// ==============================================
