<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
	id="layout-navbar">
	<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
		<a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
			<i class="bx bx-menu bx-sm"></i>
		</a>
	</div>

	<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
		<div class="navbar-nav align-items-center">
			<div class="nav-item d-flex align-items-center">
				<span class="m-date-badge">
					<i class="bx bx-calendar"></i>
					{{ date('D') }}, {{ date('d-m-Y') }}
				</span>
			</div>
		</div>

		<div class="navbar-nav align-items-center">
			<div class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown">
					<i class="bx bx-globe bx-sm"></i>
					<span class="d-none d-xl-inline-block ms-1">{{ strtoupper($currentLocale) }}</span>
				</a>
				<ul class="dropdown-menu dropdown-menu-end">
					@foreach($availableLanguages as $lang)
					<li>
						<form action="{{ route('admin.switch-language') }}" method="POST" class="d-inline">
							@csrf
							<input type="hidden" name="switch_language" value="1">
							<input type="hidden" name="locale" value="{{ $lang->code }}">
							<button type="submit" class="dropdown-item {{ $currentLocale === $lang->code ? 'active' : '' }}">
								{{ $lang->native_name ?? $lang->name }}
							</button>
						</form>
					</li>
					@endforeach
				</ul>
			</div>
		</div>

		@if(Auth::user()->hasRole('admin'))
		<div class="navbar-nav align-items-center">
			<div class="nav-item dropdown">
				<a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
					<i class="bx bx-building-house me-1"></i>
					<span>{{ App\Models\Hotel::find(session('active_hotel_id'))?->name ?? 'Select Property' }}</span>
				</a>
				<ul class="dropdown-menu dropdown-menu-end">
					@foreach(App\Models\Hotel::where('status', 'active')->get() as $hotel)
					<li>
						<form action="{{ route('admin.switch-hotel') }}" method="POST">
							@csrf
							<input type="hidden" name="switch_hotel" value="1">
							<input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
							<button type="submit" class="dropdown-item {{ session('active_hotel_id') == $hotel->id ? 'active' : '' }}">
								<i class="bx bx-building me-2"></i>{{ $hotel->name }}
							</button>
						</form>
					</li>
					@endforeach
				</ul>
			</div>
		</div>
		@endif

		<ul class="navbar-nav flex-row align-items-center ms-auto" style="gap: 0.5rem;">
			<li class="nav-item navbar-dropdown dropdown-notification dropdown">
				<a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
					<i class="bx bx-bell bx-sm"></i>
					<span class="badge bg-danger rounded-pill badge-notif-count" style="display:none;position:absolute;top:-2px;right:-2px;font-size:10px;">0</span>
				</a>
				<ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
					<li class="dropdown-header d-flex align-items-center justify-content-between">
						<span>Notifications</span>
						<form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
							@csrf
							<button type="submit" class="btn btn-sm btn-outline-primary">Mark all read</button>
						</form>
					</li>
					<li><hr class="dropdown-divider"></li>
					<div id="notification-dropdown-list" class="px-2">
						<p class="text-center text-muted py-3">Loading...</p>
					</div>
					<li><hr class="dropdown-divider"></li>
					<li class="dropdown-footer">
						<a href="{{ route('admin.notifications.index') }}" class="btn btn-primary btn-sm w-100">View All Notifications</a>
					</li>
				</ul>
			</li>
			<li class="nav-item navbar-dropdown dropdown-user dropdown">
				<a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
					data-bs-toggle="dropdown">
					<div class="avatar avatar-online">
						@if(!empty(Auth::user()->avatar) && file_exists(public_path(Auth::user()->avatar)))
						<img src="{{ asset(Auth::user()->avatar) }}" alt="User Image" class="w-px-40 h-auto rounded-circle" />
						@else
						<img src="{{ asset('assets/admin/img/avatars/1.png') }}" alt="User Image" class="w-px-40 h-auto rounded-circle" />
						@endif
					</div>
				</a>
				<ul class="dropdown-menu dropdown-menu-end">
					<li>
						<a class="dropdown-item" href="{{ route('admin.profile') }}">
							<div class="d-flex">
								<div class="flex-shrink-0 me-3">
									<div class="avatar avatar-online">
										@if(!empty(Auth::user()->avatar) && file_exists(public_path(Auth::user()->avatar)))
										<img src="{{ asset(Auth::user()->avatar) }}" alt="User Image" class="w-px-40 h-auto rounded-circle">
										@else
										<img src="{{ asset('assets/admin/img/avatars/1.png') }}" alt="User Image" class="w-px-40 h-auto rounded-circle">
										@endif
									</div>
								</div>
								<div class="flex-grow-1">
									<span class="fw-medium d-block">{{ Auth::user()->full_name }}</span>
									<small class="text-muted">{{ ucfirst(Auth::user()->roleDetail?->name ?? Auth::user()->role) }}</small>
								</div>
							</div>
						</a>
					</li>
					<li><div class="dropdown-divider"></div></li>
					<li>
						<a class="dropdown-item" href="{{ route('admin.profile') }}">
							<i class="bx bx-user me-2"></i>
							<span class="align-middle">My Profile</span>
						</a>
					</li>
					<li>
						<a class="dropdown-item" href="{{ route('admin.change.password') }}">
							<i class="bx bx-key me-2"></i>
							<span class="align-middle">Change Password</span>
						</a>
					</li>
					<li><div class="dropdown-divider"></div></li>
					<li>
						<a class="dropdown-item" href="{{ route('admin.logout') }}">
							<i class="bx bx-power-off me-2"></i>
							<span class="align-middle">Log Out</span>
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</nav>
