<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <div class="inline-block min-w-full align-middle">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-50/75">
                <tr>
                    {{ $header }}
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>