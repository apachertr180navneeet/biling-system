<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand demo">
		<a href="{{route('admin.dashboard')}}" class="app-brand-link">
			<span class="m-brand-mark">M</span>
			<span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
		</a>

		<a href="javascript:void(0);"
			class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
			<i class="bx bx-chevron-left bx-sm align-middle"></i>
		</a>
	</div>

	<div class="menu-inner-shadow"></div>

	<ul class="menu-inner py-1">
		<li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : ''}}">
			<a href="{{route('admin.dashboard')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="Dashboard">Dashboard</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('staff/*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-briefcase"></i>
				<div data-i18n="Staff Portal">Staff Portal</div>
			</a>
			<ul class="menu-sub">
				<li class="menu-item {{ request()->is('staff/dashboard') ? 'active' : ''}}">
					<a href="{{route('staff.dashboard')}}" class="menu-link">
						<div data-i18n="Staff Dashboard">Staff Dashboard</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('staff/my-tasks') ? 'active' : ''}}">
					<a href="{{route('staff.my-tasks')}}" class="menu-link">
						<div data-i18n="My Tasks">My Tasks</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('staff/unassigned-tasks') ? 'active' : ''}}">
					<a href="{{route('staff.unassigned-tasks')}}" class="menu-link">
						<div data-i18n="Available Tasks">Available Tasks</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('staff/housekeeping') ? 'active' : ''}}">
					<a href="{{route('staff.housekeeping')}}" class="menu-link">
						<div data-i18n="Housekeeping">Housekeeping</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('staff/maintenance') ? 'active' : ''}}">
					<a href="{{route('staff.maintenance')}}" class="menu-link">
						<div data-i18n="Maintenance">Maintenance</div>
					</a>
				</li>
			</ul>
		</li>

		@if(auth()->user()->hasAnyPermission(['users.view', 'roles.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">User Management</span>
		</li>
		<li class="menu-item {{ request()->is('admin/users*') || request()->is('admin/roles*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-group"></i>
				<div data-i18n="User Management">User Management</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('users.view'))
				<li class="menu-item {{ request()->is('admin/users*') ? 'active' : ''}}">
					<a href="{{route('admin.users.index')}}" class="menu-link">
						<div data-i18n="Users">Users</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('roles.view'))
				<li class="menu-item {{ request()->is('admin/roles*') ? 'active' : ''}}">
					<a href="{{route('admin.roles.index')}}" class="menu-link">
						<div data-i18n="Roles & Permissions">Roles & Permissions</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['company.view', 'branches.view', 'departments.view', 'designations.view', 'financial_years.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Organization</span>
		</li>
		<li class="menu-item {{ request()->is('admin/company*') || request()->is('admin/branches*') || request()->is('admin/departments*') || request()->is('admin/designations*') || request()->is('admin/financial-years*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-buildings"></i>
				<div data-i18n="Company Setup">Company Setup</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('company.view'))
				<li class="menu-item {{ request()->is('admin/company*') ? 'active' : ''}}">
					<a href="{{route('admin.company.index')}}" class="menu-link">
						<div data-i18n="Companies">Companies</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('branches.view'))
				<li class="menu-item {{ request()->is('admin/branches*') ? 'active' : ''}}">
					<a href="{{route('admin.branches.index')}}" class="menu-link">
						<div data-i18n="Branches">Branches</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('departments.view'))
				<li class="menu-item {{ request()->is('admin/departments*') ? 'active' : ''}}">
					<a href="{{route('admin.departments.index')}}" class="menu-link">
						<div data-i18n="Departments">Departments</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('designations.view'))
				<li class="menu-item {{ request()->is('admin/designations*') ? 'active' : ''}}">
					<a href="{{route('admin.designations.index')}}" class="menu-link">
						<div data-i18n="Designations">Designations</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('financial_years.view'))
				<li class="menu-item {{ request()->is('admin/financial-years*') ? 'active' : ''}}">
					<a href="{{route('admin.financial-years.index')}}" class="menu-link">
						<div data-i18n="Financial Years">Financial Years</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['currencies.view', 'taxes.view', 'tax_groups.view', 'number_series.view', 'company.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Configuration</span>
		</li>
		<li class="menu-item {{ request()->is('admin/masters/*') || request()->is('admin/timezones*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-data"></i>
				<div data-i18n="Masters">Masters</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('currencies.view'))
				<li class="menu-item {{ request()->is('admin/masters/currencies*') ? 'active' : ''}}">
					<a href="{{route('admin.masters.currencies.index')}}" class="menu-link">
						<div data-i18n="Currencies">Currencies</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('taxes.view'))
				<li class="menu-item {{ request()->is('admin/masters/taxes*') ? 'active' : ''}}">
					<a href="{{route('admin.masters.taxes.index')}}" class="menu-link">
						<div data-i18n="Taxes">Taxes</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('tax_groups.view'))
				<li class="menu-item {{ request()->is('admin/masters/tax-groups*') ? 'active' : ''}}">
					<a href="{{route('admin.masters.tax-groups.index')}}" class="menu-link">
						<div data-i18n="Tax Groups">Tax Groups</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('number_series.view'))
				<li class="menu-item {{ request()->is('admin/masters/number-series*') ? 'active' : ''}}">
					<a href="{{route('admin.masters.number-series.index')}}" class="menu-link">
						<div data-i18n="Number Series">Number Series</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('company.view'))
				<li class="menu-item {{ request()->is('admin/timezones*') ? 'active' : ''}}">
					<a href="{{route('admin.timezones.index')}}" class="menu-link">
						<div data-i18n="Timezones">Timezones</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['hotels.view', 'buildings.view', 'floors.view', 'wings.view', 'room_types.view', 'amenities.view', 'bed_types.view', 'room_status.view', 'rooms.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Property</span>
		</li>
		<li class="menu-item {{ request()->is('admin/property/hotels*') || request()->is('admin/property/buildings*') || request()->is('admin/property/floors*') || request()->is('admin/property/wings*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-building-house"></i>
				<div data-i18n="Property Master">Property Master</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('hotels.view'))
				<li class="menu-item {{ request()->is('admin/property/hotels*') ? 'active' : ''}}">
					<a href="{{route('admin.property.hotels.index')}}" class="menu-link">
						<div data-i18n="Hotels">Hotels</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('buildings.view'))
				<li class="menu-item {{ request()->is('admin/property/buildings*') ? 'active' : ''}}">
					<a href="{{route('admin.property.buildings.index')}}" class="menu-link">
						<div data-i18n="Buildings">Buildings</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('floors.view'))
				<li class="menu-item {{ request()->is('admin/property/floors*') ? 'active' : ''}}">
					<a href="{{route('admin.property.floors.index')}}" class="menu-link">
						<div data-i18n="Floors">Floors</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('wings.view'))
				<li class="menu-item {{ request()->is('admin/property/wings*') ? 'active' : ''}}">
					<a href="{{route('admin.property.wings.index')}}" class="menu-link">
						<div data-i18n="Wings">Wings</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		<li class="menu-item {{ request()->is('admin/property/room-types*') || request()->is('admin/property/amenities*') || request()->is('admin/property/bed-types*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-bed"></i>
				<div data-i18n="Room Master">Room Master</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('room_types.view'))
				<li class="menu-item {{ request()->is('admin/property/room-types*') ? 'active' : ''}}">
					<a href="{{route('admin.property.room-types.index')}}" class="menu-link">
						<div data-i18n="Room Types">Room Types</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('amenities.view'))
				<li class="menu-item {{ request()->is('admin/property/amenities*') ? 'active' : ''}}">
					<a href="{{route('admin.property.amenities.index')}}" class="menu-link">
						<div data-i18n="Amenities">Amenities</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('bed_types.view'))
				<li class="menu-item {{ request()->is('admin/property/bed-types*') ? 'active' : ''}}">
					<a href="{{route('admin.property.bed-types.index')}}" class="menu-link">
						<div data-i18n="Bed Types">Bed Types</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		<li class="menu-item {{ request()->is('admin/property/room-status*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-check-circle"></i>
				<div data-i18n="Room Status">Room Status</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('room_status.view'))
				<li class="menu-item {{ request()->is('admin/property/room-status*') ? 'active' : ''}}">
					<a href="{{route('admin.property.room-status.index')}}" class="menu-link">
						<div data-i18n="Room Status">Room Status</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@if(auth()->user()->hasPermission('rooms.view'))
		<li class="menu-item {{ request()->is('admin/property/rooms*') ? 'active' : ''}}">
			<a href="{{route('admin.property.rooms.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-door-open"></i>
				<div data-i18n="Rooms">Rooms</div>
			</a>
		</li>
		@endif
		@endif

		@if(auth()->user()->hasAnyPermission(['bookings.view', 'group_bookings.view', 'guests.view', 'rate_plans.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Reservation</span>
		</li>
		<li class="menu-item {{ request()->is('admin/reservation/bookings*') || request()->is('admin/reservation/group-bookings*') || request()->is('admin/reservation/calendar*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-calendar"></i>
				<div data-i18n="Reservations">Reservations</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('bookings.view'))
				<li class="menu-item {{ request()->is('admin/reservation/bookings*') ? 'active' : ''}}">
					<a href="{{route('admin.reservation.bookings.index')}}" class="menu-link">
						<div data-i18n="Bookings">Bookings</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('group_bookings.view'))
				<li class="menu-item {{ request()->is('admin/reservation/group-bookings*') ? 'active' : ''}}">
					<a href="{{route('admin.reservation.group-bookings.index')}}" class="menu-link">
						<div data-i18n="Group Bookings">Group Bookings</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('bookings.view'))
				<li class="menu-item {{ request()->is('admin/reservation/calendar*') ? 'active' : ''}}">
					<a href="{{route('admin.reservation.calendar')}}" class="menu-link">
						<div data-i18n="Calendar">Calendar</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@if(auth()->user()->hasAnyPermission(['guests.view', 'rate_plans.view']))
		<li class="menu-item {{ request()->is('admin/reservation/guests*') || request()->is('admin/reservation/rate-plans*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-user"></i>
				<div data-i18n="Reservation Setup">Reservation Setup</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('guests.view'))
				<li class="menu-item {{ request()->is('admin/reservation/guests*') ? 'active' : ''}}">
					<a href="{{route('admin.reservation.guests.index')}}" class="menu-link">
						<div data-i18n="Guests">Guests</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('rate_plans.view'))
				<li class="menu-item {{ request()->is('admin/reservation/rate-plans*') ? 'active' : ''}}">
					<a href="{{route('admin.reservation.rate-plans.index')}}" class="menu-link">
						<div data-i18n="Rate Plans">Rate Plans</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif
		@endif

		@if(auth()->user()->hasAnyPermission(['channel_manager.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Channel Manager</span>
		</li>
		<li class="menu-item {{ request()->is('admin/channel-manager/channels*') || request()->is('admin/channel-manager/mappings*') || request()->is('admin/channel-manager/sync-logs*') || request()->is('admin/channel-manager/settings*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-link"></i>
				<div data-i18n="Channel Manager">Channel Manager</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('channel_manager.view'))
				<li class="menu-item {{ request()->is('admin/channel-manager/channels*') ? 'active' : ''}}">
					<a href="{{route('admin.channel-manager.channels.index')}}" class="menu-link">
						<div data-i18n="OTA Channels">OTA Channels</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/channel-manager/mappings*') ? 'active' : ''}}">
					<a href="{{route('admin.channel-manager.mappings.index')}}" class="menu-link">
						<div data-i18n="Room Mappings">Room Mappings</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/channel-manager/sync-logs*') ? 'active' : ''}}">
					<a href="{{route('admin.channel-manager.sync-logs.index')}}" class="menu-link">
						<div data-i18n="Sync Logs">Sync Logs</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('channel_manager.edit'))
				<li class="menu-item {{ request()->is('admin/channel-manager/settings*') ? 'active' : ''}}">
					<a href="{{route('admin.channel-manager.settings')}}" class="menu-link">
						<div data-i18n="Settings">Settings</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('channel_manager.view'))
				<li class="menu-item {{ request()->is('admin/channel-manager/test-api*') ? 'active' : ''}}">
					<a href="{{route('admin.channel-manager.test-api')}}" class="menu-link">
						<div data-i18n="Test API">Test API</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['check_ins.view', 'check_outs.view', 'night_audits.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Front Office</span>
		</li>
		<li class="menu-item {{ request()->is('admin/front-office/check-ins*') || request()->is('admin/front-office/check-outs*') || request()->is('admin/front-office/night-audits*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-building"></i>
				<div data-i18n="Front Office">Front Office</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('check_ins.view'))
				<li class="menu-item {{ request()->is('admin/front-office/check-ins*') ? 'active' : ''}}">
					<a href="{{route('admin.front-office.check-ins.index')}}" class="menu-link">
						<div data-i18n="Check In">Check In</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('check_outs.view'))
				<li class="menu-item {{ request()->is('admin/front-office/check-outs*') ? 'active' : ''}}">
					<a href="{{route('admin.front-office.check-outs.index')}}" class="menu-link">
						<div data-i18n="Check Out">Check Out</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('night_audits.view'))
				<li class="menu-item {{ request()->is('admin/front-office/night-audits*') ? 'active' : ''}}">
					<a href="{{route('admin.front-office.night-audits.index')}}" class="menu-link">
						<div data-i18n="Night Audit">Night Audit</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['cleaning_schedules.view', 'laundry_items.view', 'laundry_orders.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Housekeeping</span>
		</li>
		<li class="menu-item {{ request()->is('admin/housekeeping/cleaning-schedules*') || request()->is('admin/housekeeping/laundry-items*') || request()->is('admin/housekeeping/laundry-orders*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-spray-can"></i>
				<div data-i18n="Housekeeping">Housekeeping</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('cleaning_schedules.view'))
				<li class="menu-item {{ request()->is('admin/housekeeping/cleaning-schedules*') ? 'active' : ''}}">
					<a href="{{route('admin.housekeeping.cleaning-schedules.index')}}" class="menu-link">
						<div data-i18n="Cleaning Schedule">Cleaning Schedule</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasAnyPermission(['laundry_items.view', 'laundry_orders.view']))
				<li class="menu-item {{ request()->is('admin/housekeeping/laundry-items*') || request()->is('admin/housekeeping/laundry-orders*') ? 'active open' : ''}}">
					<a href="javascript:void(0);" class="menu-link menu-toggle">
						<div data-i18n="Laundry">Laundry</div>
					</a>
					<ul class="menu-sub">
						@if(auth()->user()->hasPermission('laundry_items.view'))
						<li class="menu-item {{ request()->is('admin/housekeeping/laundry-items*') ? 'active' : ''}}">
							<a href="{{route('admin.housekeeping.laundry-items.index')}}" class="menu-link">
								<div data-i18n="Laundry Items">Linen & Items</div>
							</a>
						</li>
						@endif
						@if(auth()->user()->hasPermission('laundry_orders.view'))
						<li class="menu-item {{ request()->is('admin/housekeeping/laundry-orders*') ? 'active' : ''}}">
							<a href="{{route('admin.housekeeping.laundry-orders.index')}}" class="menu-link">
								<div data-i18n="Laundry Orders">Laundry Orders</div>
							</a>
						</li>
						@endif
					</ul>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['restaurant_tables.view', 'menu_items.view', 'restaurant_orders.view', 'room_service.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Restaurant POS</span>
		</li>
		<li class="menu-item {{ request()->is('admin/restaurant/tables*') || request()->is('admin/restaurant/menu-items*') || request()->is('admin/restaurant/orders*') || request()->is('admin/restaurant/room-service*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-dish"></i>
				<div data-i18n="Restaurant POS">Restaurant POS</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('restaurant_tables.view'))
				<li class="menu-item {{ request()->is('admin/restaurant/tables*') ? 'active' : ''}}">
					<a href="{{route('admin.restaurant.tables.index')}}" class="menu-link">
						<div data-i18n="Tables">Tables</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('menu_items.view'))
				<li class="menu-item {{ request()->is('admin/restaurant/menu-items*') ? 'active' : ''}}">
					<a href="{{route('admin.restaurant.menu-items.index')}}" class="menu-link">
						<div data-i18n="Menu Items">Menu Items</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('restaurant_orders.view'))
				<li class="menu-item {{ request()->is('admin/restaurant/orders*') ? 'active' : ''}}">
					<a href="{{route('admin.restaurant.orders.index')}}" class="menu-link">
						<div data-i18n="Orders (KOT & Billing)">Orders (KOT)</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('room_service.view'))
				<li class="menu-item {{ request()->is('admin/restaurant/room-service*') ? 'active' : ''}}">
					<a href="{{route('admin.restaurant.room-service.index')}}" class="menu-link">
						<div data-i18n="Room Service">Room Service</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['inventory_categories.view', 'inventory_units.view', 'inventory_items.view', 'inventory_suppliers.view', 'inventory_purchase_orders.view', 'inventory_grn.view', 'inventory_stocks.view', 'inventory_stock_transfers.view', 'inventory_stock_adjustments.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Inventory</span>
		</li>
		<li class="menu-item {{ request()->is('admin/inventory/categories*') || request()->is('admin/inventory/units*') || request()->is('admin/inventory/items*') || request()->is('admin/inventory/suppliers*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-package"></i>
				<div data-i18n="Item Master">Item Master</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('inventory_categories.view'))
				<li class="menu-item {{ request()->is('admin/inventory/categories*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.categories.index')}}" class="menu-link">
						<div data-i18n="Categories">Categories</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('inventory_units.view'))
				<li class="menu-item {{ request()->is('admin/inventory/units*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.units.index')}}" class="menu-link">
						<div data-i18n="Units">Units</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('inventory_items.view'))
				<li class="menu-item {{ request()->is('admin/inventory/items*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.items.index')}}" class="menu-link">
						<div data-i18n="Items">Items</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('inventory_suppliers.view'))
				<li class="menu-item {{ request()->is('admin/inventory/suppliers*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.suppliers.index')}}" class="menu-link">
						<div data-i18n="Suppliers">Suppliers</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@if(auth()->user()->hasAnyPermission(['inventory_purchase_orders.view', 'inventory_grn.view']))
		<li class="menu-item {{ request()->is('admin/inventory/purchase-orders*') || request()->is('admin/inventory/grns*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-cart"></i>
				<div data-i18n="Purchase">Purchase</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('inventory_purchase_orders.view'))
				<li class="menu-item {{ request()->is('admin/inventory/purchase-orders*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.purchase-orders.index')}}" class="menu-link">
						<div data-i18n="Purchase Orders">Purchase Orders</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('inventory_grn.view'))
				<li class="menu-item {{ request()->is('admin/inventory/grns*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.grns.index')}}" class="menu-link">
						<div data-i18n="GRN">GRN</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif
		@if(auth()->user()->hasAnyPermission(['inventory_stocks.view', 'inventory_stock_transfers.view', 'inventory_stock_adjustments.view']))
		<li class="menu-item {{ request()->is('admin/inventory/stocks*') || request()->is('admin/inventory/stock-transfers*') || request()->is('admin/inventory/stock-adjustments*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-transfer"></i>
				<div data-i18n="Stock">Stock</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('inventory_stocks.view'))
				<li class="menu-item {{ request()->is('admin/inventory/stocks*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.stocks.index')}}" class="menu-link">
						<div data-i18n="Current Stock">Current Stock</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('inventory_stock_transfers.view'))
				<li class="menu-item {{ request()->is('admin/inventory/stock-transfers*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.stock-transfers.index')}}" class="menu-link">
						<div data-i18n="Stock Transfers">Stock Transfers</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('inventory_stock_adjustments.view'))
				<li class="menu-item {{ request()->is('admin/inventory/stock-adjustments*') ? 'active' : ''}}">
					<a href="{{route('admin.inventory.stock-adjustments.index')}}" class="menu-link">
						<div data-i18n="Stock Adjustments">Stock Adjustments</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif
		@endif

		@if(auth()->user()->hasAnyPermission(['guest_profiles.view', 'loyalty_members.view', 'loyalty_tiers.view', 'marketing_campaigns.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Guest CRM</span>
		</li>
		<li class="menu-item {{ request()->is('admin/crm/guest-profiles*') || request()->is('admin/crm/loyalty*') || request()->is('admin/crm/campaigns*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-heart"></i>
				<div data-i18n="Guest CRM">Guest CRM</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('guest_profiles.view'))
				<li class="menu-item {{ request()->is('admin/crm/guest-profiles*') ? 'active' : ''}}">
					<a href="{{route('admin.crm.guest-profiles.index')}}" class="menu-link">
						<div data-i18n="Guest Profiles">Guest Profiles</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasAnyPermission(['loyalty_members.view', 'loyalty_tiers.view']))
				<li class="menu-item {{ request()->is('admin/crm/loyalty*') ? 'active' : ''}}">
					<a href="{{route('admin.crm.loyalty.index')}}" class="menu-link">
						<div data-i18n="Loyalty">Loyalty</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('marketing_campaigns.view'))
				<li class="menu-item {{ request()->is('admin/crm/campaigns*') ? 'active' : ''}}">
					<a href="{{route('admin.crm.campaigns.index')}}" class="menu-link">
						<div data-i18n="Campaigns">Campaigns</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['vendor_categories.view', 'vendors.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Procurement</span>
		</li>
		<li class="menu-item {{ request()->is('admin/procurement/*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-cart"></i>
				<div data-i18n="Vendor">Vendor</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('vendors.view'))
				<li class="menu-item {{ request()->is('admin/procurement/vendors*') ? 'active' : ''}}">
					<a href="{{route('admin.procurement.vendors.index')}}" class="menu-link">
						<div data-i18n="Vendor Master">Vendor Master</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('vendor_categories.view'))
				<li class="menu-item {{ request()->is('admin/procurement/vendor-categories*') ? 'active' : ''}}">
					<a href="{{route('admin.procurement.vendor-categories.index')}}" class="menu-link">
						<div data-i18n="Vendor Categories">Vendor Categories</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['halls.view', 'events.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Banquet</span>
		</li>
		<li class="menu-item {{ request()->is('admin/banquet/*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-calendar-event"></i>
				<div data-i18n="Events">Events</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('events.view'))
				<li class="menu-item {{ request()->is('admin/banquet/events*') ? 'active' : ''}}">
					<a href="{{route('admin.banquet.events.index')}}" class="menu-link">
						<div data-i18n="Hall Booking">Hall Booking</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('halls.view'))
				<li class="menu-item {{ request()->is('admin/banquet/halls*') ? 'active' : ''}}">
					<a href="{{route('admin.banquet.halls.index')}}" class="menu-link">
						<div data-i18n="Halls">Halls</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['spa_services.view', 'spa_appointments.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Spa</span>
		</li>
		<li class="menu-item {{ request()->is('admin/spa/*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-spa"></i>
				<div data-i18n="Spa">Spa</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('spa_services.view'))
				<li class="menu-item {{ request()->is('admin/spa/services*') ? 'active' : ''}}">
					<a href="{{route('admin.spa.services.index')}}" class="menu-link">
						<div data-i18n="Services">Services</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('spa_appointments.view'))
				<li class="menu-item {{ request()->is('admin/spa/appointments*') ? 'active' : ''}}">
					<a href="{{route('admin.spa.appointments.index')}}" class="menu-link">
						<div data-i18n="Appointments">Appointments</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['transport_types.view', 'transport_bookings.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Travel Desk</span>
		</li>
		<li class="menu-item {{ request()->is('admin/travel-desk/*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-car"></i>
				<div data-i18n="Travel Desk">Travel Desk</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('transport_bookings.view'))
				<li class="menu-item {{ request()->is('admin/travel-desk/bookings*') ? 'active' : ''}}">
					<a href="{{route('admin.travel-desk.bookings.index')}}" class="menu-link">
						<div data-i18n="Pickup & Drop">Pickup & Drop</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('transport_types.view'))
				<li class="menu-item {{ request()->is('admin/travel-desk/types*') ? 'active' : ''}}">
					<a href="{{route('admin.travel-desk.types.index')}}" class="menu-link">
						<div data-i18n="Transport Types">Transport Types</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['chart_of_accounts.view', 'journal_entries.view', 'accounts_payable.view', 'accounts_receivable.view', 'gst_returns.view', 'expenses.view', 'finance_reports.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Finance</span>
		</li>
		<li class="menu-item {{ request()->is('admin/finance/chart-of-accounts*') || request()->is('admin/finance/journal-entries*') || request()->is('admin/finance/accounts-payable*') || request()->is('admin/finance/accounts-receivable*') || request()->is('admin/finance/gst*') || request()->is('admin/finance/expenses*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-dollar"></i>
				<div data-i18n="Finance">Finance</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('chart_of_accounts.view'))
				<li class="menu-item {{ request()->is('admin/finance/chart-of-accounts*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.chart-of-accounts.index')}}" class="menu-link">
						<div data-i18n="Chart of Accounts">Chart of Accounts</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('journal_entries.view'))
				<li class="menu-item {{ request()->is('admin/finance/journal-entries*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.journal-entries.index')}}" class="menu-link">
						<div data-i18n="Journal Entries">Journal Entries</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasAnyPermission(['accounts_payable.view']))
				<li class="menu-item {{ request()->is('admin/finance/accounts-payable*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.accounts-payable.index')}}" class="menu-link">
						<div data-i18n="Accounts Payable">Accounts Payable</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasAnyPermission(['accounts_receivable.view']))
				<li class="menu-item {{ request()->is('admin/finance/accounts-receivable*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.accounts-receivable.index')}}" class="menu-link">
						<div data-i18n="Accounts Receivable">Accounts Receivable</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('gst_returns.view'))
				<li class="menu-item {{ request()->is('admin/finance/gst') ? 'active' : ''}}">
					<a href="{{route('admin.finance.gst.index')}}" class="menu-link">
						<div data-i18n="GST Returns">GST Returns</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/finance/gst/summary') ? 'active' : ''}}">
					<a href="{{route('admin.finance.gst.summary')}}" class="menu-link">
						<div data-i18n="GST Summary">GST Summary</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/finance/hsn-sac-codes*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.hsn-sac-codes.index')}}" class="menu-link">
						<div data-i18n="HSN/SAC Codes">HSN/SAC Codes</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('expenses.view'))
				<li class="menu-item {{ request()->is('admin/finance/expenses*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.expenses.index')}}" class="menu-link">
						<div data-i18n="Expenses">Expenses</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		<li class="menu-item {{ request()->is('admin/finance/reports*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-bar-chart"></i>
				<div data-i18n="Reports">Reports</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('finance_reports.view'))
				<li class="menu-item {{ request()->is('admin/finance/reports/dashboard*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.reports.dashboard')}}" class="menu-link">
						<div data-i18n="Revenue Dashboard">Revenue Dashboard</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/finance/reports/trial-balance*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.reports.trial-balance')}}" class="menu-link">
						<div data-i18n="Trial Balance">Trial Balance</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/finance/reports/profit-loss*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.reports.profit-loss')}}" class="menu-link">
						<div data-i18n="Profit & Loss">Profit & Loss</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/finance/reports/balance-sheet*') ? 'active' : ''}}">
					<a href="{{route('admin.finance.reports.balance-sheet')}}" class="menu-link">
						<div data-i18n="Balance Sheet">Balance Sheet</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasPermission('payment_gateways.view'))
		<li class="menu-item {{ request()->is('admin/payment-gateways*') ? 'active' : ''}}">
			<a href="{{route('admin.payment-gateways.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-credit-card"></i>
				<div data-i18n="Payment Gateways">Payment Gateways</div>
			</a>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['hr_employees.view', 'hr_attendance.view', 'hr_payroll.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">HR & Payroll</span>
		</li>
		<li class="menu-item {{ request()->is('admin/hr/employees*') || request()->is('admin/hr/attendance*') || request()->is('admin/hr/payroll*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-user"></i>
				<div data-i18n="HR & Payroll">HR & Payroll</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('hr_employees.view'))
				<li class="menu-item {{ request()->is('admin/hr/employees*') ? 'active' : ''}}">
					<a href="{{route('admin.hr.employees.index')}}" class="menu-link">
						<div data-i18n="Employees">Employees</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('hr_attendance.view'))
				<li class="menu-item {{ request()->is('admin/hr/attendance*') ? 'active' : ''}}">
					<a href="{{route('admin.hr.attendance.index')}}" class="menu-link">
						<div data-i18n="Attendance">Attendance</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('hr_payroll.view'))
				<li class="menu-item {{ request()->is('admin/hr/payroll*') ? 'active' : ''}}">
					<a href="{{route('admin.hr.payroll.index')}}" class="menu-link">
						<div data-i18n="Payroll">Payroll</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['maintenance_assets.view', 'maintenance_amcs.view', 'maintenance_work_orders.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Maintenance</span>
		</li>
		<li class="menu-item {{ request()->is('admin/maintenance/assets*') || request()->is('admin/maintenance/amcs*') || request()->is('admin/maintenance/work-orders*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-wrench"></i>
				<div data-i18n="Maintenance">Maintenance</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('maintenance_assets.view'))
				<li class="menu-item {{ request()->is('admin/maintenance/assets*') ? 'active' : ''}}">
					<a href="{{route('admin.maintenance.assets.index')}}" class="menu-link">
						<div data-i18n="Assets">Assets</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('maintenance_amcs.view'))
				<li class="menu-item {{ request()->is('admin/maintenance/amcs*') ? 'active' : ''}}">
					<a href="{{route('admin.maintenance.amcs.index')}}" class="menu-link">
						<div data-i18n="AMC">AMC</div>
					</a>
				</li>
				@endif
				@if(auth()->user()->hasPermission('maintenance_work_orders.view'))
				<li class="menu-item {{ request()->is('admin/maintenance/work-orders*') ? 'active' : ''}}">
					<a href="{{route('admin.maintenance.work-orders.index')}}" class="menu-link">
						<div data-i18n="Work Orders">Work Orders</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['devices.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">Integrations</span>
		</li>
		<li class="menu-item {{ request()->is('admin/integrations/*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-chip"></i>
				<div data-i18n="Devices">Devices</div>
			</a>
			<ul class="menu-sub">
				@if(auth()->user()->hasPermission('devices.view'))
				<li class="menu-item {{ request()->is('admin/integrations/devices*') ? 'active' : ''}}">
					<a href="{{route('admin.integrations.devices.index')}}" class="menu-link">
						<div data-i18n="All Devices">All Devices</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/integrations/logs*') ? 'active' : ''}}">
					<a href="{{route('admin.integrations.logs.index')}}" class="menu-link">
						<div data-i18n="Device Logs">Device Logs</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/integrations/access-codes*') ? 'active' : ''}}">
					<a href="{{route('admin.integrations.access-codes.index')}}" class="menu-link">
						<div data-i18n="Access Codes">Access Codes</div>
					</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(auth()->user()->hasAnyPermission(['dashboard.view']))
		<li class="menu-header small text-uppercase">
			<span class="menu-header-text">System</span>
		</li>
		<li class="menu-item {{ request()->is('admin/audit-logs*') || request()->is('admin/approvals*') || request()->is('admin/notifications*') || request()->is('admin/communications*') || request()->is('admin/languages*') ? 'active open' : ''}}">
			<a href="javascript:void(0);" class="menu-link menu-toggle">
				<i class="menu-icon tf-icons bx bx-slider"></i>
				<div data-i18n="Foundation">Foundation</div>
			</a>
			<ul class="menu-sub">
				<li class="menu-item {{ request()->is('admin/audit-logs*') ? 'active' : ''}}">
					<a href="{{route('admin.audit-logs.index')}}" class="menu-link">
						<div data-i18n="Audit Logs">Audit Logs</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/approvals*') ? 'active' : ''}}">
					<a href="{{route('admin.approvals.index')}}" class="menu-link">
						<div data-i18n="Approvals">Approvals</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/communications*') ? 'active' : ''}}">
					<a href="{{route('admin.communications.index')}}" class="menu-link">
						<div data-i18n="Templates">Templates</div>
					</a>
				</li>
				<li class="menu-item {{ request()->is('admin/languages*') ? 'active' : ''}}">
					<a href="{{route('admin.languages.index')}}" class="menu-link">
						<div data-i18n="Languages">Languages</div>
					</a>
				</li>
			</ul>
		</li>
		@endif
	</ul>
</aside>
