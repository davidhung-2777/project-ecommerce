<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Products Page - UI Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cream': '#F5F0E8', 'beige': '#E8DFD0', 'sand': '#D4C4A8',
                        'wood': '#A0856A', 'wooddk': '#6B5744', 'charcoal': '#2C2C2C', 'muted': '#6B6B6B',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg mb-6">
            <h2 class="font-bold mb-2">🧪 Test Products Page UI</h2>
            <p class="text-sm">This page tests if there are any unexpected overlays, purple blocks, or UI issues.</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-6">
            <h3 class="text-xl font-bold mb-4">UI Elements Test</h3>
            
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <strong>✓ No purple overlay:</strong> If you see this text clearly, there's no purple block.
                </div>
                
                <div class="p-4 bg-green-50 text-green-800 rounded-lg">
                    <strong>✓ Green block visible:</strong> This should be green background.
                </div>
                
                <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg">
                    <strong>✓ Yellow block visible:</strong> This should be yellow background.
                </div>
                
                <div class="p-4 bg-red-50 text-red-800 rounded-lg">
                    <strong>✓ Red block visible:</strong> This should be red background.
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-6">
            <h3 class="text-xl font-bold mb-4">Z-Index Layers Test</h3>
            
            <div class="relative h-48 bg-gray-100 rounded-lg overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-gray-300">
                    BACKGROUND
                </div>
                <div class="absolute top-4 left-4 bg-white p-4 shadow-lg rounded-lg z-10">
                    <strong>Floating Card (z-10)</strong>
                    <p class="text-sm text-gray-600">This should be on top</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 class="text-xl font-bold mb-4">Actual Products Table Preview</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-gray-700 uppercase">Sản phẩm</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-gray-700 uppercase">SKU</th>
                            <th class="text-right px-6 py-4 text-xs font-semibold text-gray-700 uppercase">Giá</th>
                            <th class="text-center px-6 py-4 text-xs font-semibold text-gray-700 uppercase">Tồn kho</th>
                            <th class="text-center px-6 py-4 text-xs font-semibold text-gray-700 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-medium">Giường ngủ Scandinavian</p>
                                        <p class="text-xs text-gray-500">Giường ngủ</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="font-mono text-xs">SKU001</span></td>
                            <td class="px-6 py-4 text-right"><strong>15,000,000đ</strong></td>
                            <td class="px-6 py-4 text-center"><span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">50</span></td>
                            <td class="px-6 py-4 text-center"><span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs">✓ Hiển thị</span></td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-medium">Tủ quần áo Japandi</p>
                                        <p class="text-xs text-gray-500">Tủ quần áo</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="font-mono text-xs">SKU002</span></td>
                            <td class="px-6 py-4 text-right"><strong>8,500,000đ</strong></td>
                            <td class="px-6 py-4 text-center"><span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs">3</span></td>
                            <td class="px-6 py-4 text-center"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">Ẩn</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            <strong>✅ If you can see all elements clearly without any purple overlay:</strong>
            <ul class="text-sm mt-2 space-y-1">
                <li>→ Go to: <a href="/project-ecommerce/public/admin/products" class="underline font-bold">/admin/products</a></li>
                <li>→ The UI should now be fixed!</li>
            </ul>
        </div>
    </div>
</body>
</html>
