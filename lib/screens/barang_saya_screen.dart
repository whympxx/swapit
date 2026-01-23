import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/item_provider.dart';
import '../providers/auth_provider.dart';
import '../widgets/custom_image.dart';

class BarangSayaScreen extends StatelessWidget {
  const BarangSayaScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final itemProvider = Provider.of<ItemProvider>(context);
    final authProvider = Provider.of<AuthProvider>(context);
    final myItems = itemProvider.getItemsByUserId(authProvider.currentUser?.id ?? '');

    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text('Barang Saya'),
        backgroundColor: const Color(0xFF5B4DFF),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: () {
              Navigator.pushNamed(context, '/posting');
            },
          ),
        ],
      ),
      body: myItems.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.inventory_2_outlined, size: 80, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  Text(
                    'Belum ada barang',
                    style: TextStyle(fontSize: 16, color: Colors.grey[600]),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: () {
                      Navigator.pushNamed(context, '/posting');
                    },
                    icon: const Icon(Icons.add),
                    label: const Text('Posting Barang'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF5B4DFF),
                      foregroundColor: Colors.white,
                    ),
                  ),
                ],
              ),
            )
          : ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: myItems.length,
              itemBuilder: (context, index) {
                final item = myItems[index];
                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: ListTile(
                    leading: ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: item.images.isNotEmpty
                          ? CustomImage(
                              imageUrl: item.images[0],
                              width: 60,
                              height: 60,
                              fit: BoxFit.cover,
                            )
                          : Container(
                              width: 60,
                              height: 60,
                              color: Colors.grey[200],
                              child: const Icon(Icons.image, color: Colors.grey),
                            ),
                    ),
                    title: Text(item.title),
                    subtitle: Text(item.category),
                    trailing: PopupMenuButton(
                      itemBuilder: (context) => [
                        const PopupMenuItem(
                          value: 'edit',
                          child: Text('Edit'),
                        ),
                        const PopupMenuItem(
                          value: 'delete',
                          child: Text('Hapus'),
                        ),
                      ],
                      onSelected: (value) {
                        if (value == 'delete') {
                          itemProvider.deleteItem(item.id);
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Barang berhasil dihapus')),
                          );
                        }
                      },
                    ),
                  ),
                );
              },
            ),
    );
  }
}
