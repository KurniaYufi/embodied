<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <p class="mb-1 text-xs tracking-[0.3em] text-neutral-500 uppercase">Overview</p>
            <h1 class="font-serif text-3xl">Dashboard</h1>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="border border-neutral-200 bg-white p-6">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Orders Today</p>
                <p class="font-serif text-3xl">0</p>
            </div>
            <div class="border border-neutral-200 bg-white p-6">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">Revenue Today</p>
                <p class="font-serif text-3xl">Rp 0</p>
            </div>
            <div class="border border-neutral-200 bg-white p-6">
                <p class="mb-2 text-xs tracking-[0.2em] text-neutral-500 uppercase">New Customers</p>
                <p class="font-serif text-3xl">0</p>
            </div>
        </div>

        <div class="h-full flex-1 border border-neutral-200 bg-white p-6">
            <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">Recent Activity</p>
            <p class="text-sm text-neutral-500">Nothing to show yet.</p>
        </div>
    </div>
</x-layouts::app>
