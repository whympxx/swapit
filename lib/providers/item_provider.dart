import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/item.dart';

class ItemProvider with ChangeNotifier {
  List<Item> _items = [];
  bool _isLoaded = false;

  ItemProvider() {
    _loadItems();
  }

  // Load items from SharedPreferences
  Future<void> _loadItems() async {
    if (_isLoaded) return;
    
    final prefs = await SharedPreferences.getInstance();
    final String? itemsJson = prefs.getString('items');
    
    if (itemsJson != null && itemsJson.isNotEmpty) {
      try {
        final List<dynamic> decoded = json.decode(itemsJson);
        _items = decoded.map((item) => Item.fromJson(item)).toList();
      } catch (e) {
        print('Error loading items: $e');
        _items = _getDefaultItems();
      }
    } else {
      _items = _getDefaultItems();
      await _saveItems(); // Save default items
    }
    
    _isLoaded = true;
    notifyListeners();
  }

  // Save items to SharedPreferences
  Future<void> _saveItems() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final String itemsJson = json.encode(_items.map((item) => item.toJson()).toList());
      await prefs.setString('items', itemsJson);
    } catch (e) {
      print('Error saving items: $e');
    }
  }

  // Get default items (initial data)
  List<Item> _getDefaultItems() {
    return [
    Item(
      id: '1',
      title: 'Laptop Gaming ROG Strix',
      description: 'Laptop gaming ROG Strix dengan keyboard RGB, kondisi masih bagus, RAM 16GB, SSD 512GB, layar 15.6 inch 144Hz',
      category: 'Elektronik',
      condition: 'Bekas - Seperti Baru',
      images: [
        'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800',
        'https://images.unsplash.com/photo-1625019030820-e4ed970a6c95?w=800',
      ],
      userId: '2',
      userName: 'Ahmad Rizki',
      createdAt: DateTime.now().subtract(const Duration(days: 2)),
    ),
    Item(
      id: '2',
      title: 'Sepatu Nike Dunk Low',
      description: 'Sepatu Nike Dunk Low Navy/Brown, size 42, kondisi masih sangat bagus, box masih ada',
      category: 'Fashion',
      condition: 'Bekas - Baik',
      images: [
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800',
        'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=800',
      ],
      userId: '3',
      userName: 'Siti Nurhaliza',
      createdAt: DateTime.now().subtract(const Duration(days: 1)),
    ),
    Item(
      id: '3',
      title: 'Buku "Build Your Own Programming Language"',
      description: 'Buku panduan lengkap membuat bahasa pemrograman sendiri. Second Edition by Clinton L. Jeffery. Kondisi masih bagus, tidak ada coretan',
      category: 'Buku',
      condition: 'Bekas - Baik',
      images: [
        'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=800',
        'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=800',
      ],
      userId: '4',
      userName: 'Budi Santoso',
      createdAt: DateTime.now().subtract(const Duration(hours: 5)),
    ),
    Item(
      id: '4',
      title: 'Keyboard Mechanical RGB',
      description: 'Keyboard mechanical dengan lampu RGB, switch blue, cocok untuk gaming dan coding',
      category: 'Elektronik',
      condition: 'Bekas - Seperti Baru',
      images: [
        'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800',
      ],
      userId: '2',
      userName: 'Ahmad Rizki',
      createdAt: DateTime.now().subtract(const Duration(hours: 3)),
    ),
    Item(
      id: '5',
      title: 'Tas Ransel Laptop',
      description: 'Tas ransel untuk laptop 15 inch, banyak kantong, nyaman dipakai',
      category: 'Fashion',
      condition: 'Bekas - Baik',
      images: [
        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800',
      ],
      userId: '5',
      userName: 'Dewi Lestari',
      createdAt: DateTime.now().subtract(const Duration(hours: 8)),
    ),
    Item(
      id: '6',
      title: 'Buku Algoritma & Struktur Data',
      description: 'Buku referensi algoritma dan struktur data, lengkap dengan contoh kode',
      category: 'Buku',
      condition: 'Bekas - Baik',
      images: [
        'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=800',
      ],
      userId: '4',
      userName: 'Budi Santoso',
      createdAt: DateTime.now().subtract(const Duration(days: 3)),
    ),
    ];
  }

  List<Item> get items => _items;
  bool get isLoaded => _isLoaded;

  List<Item> getItemsByUserId(String userId) {
    return _items.where((item) => item.userId == userId).toList();
  }

  Item? getItemById(String id) {
    try {
      return _items.firstWhere((item) => item.id == id);
    } catch (e) {
      return null;
    }
  }

  Future<void> addItem(Item item) async {
    _items.insert(0, item);
    await _saveItems();
    notifyListeners();
  }

  Future<void> updateItem(String id, Item updatedItem) async {
    final index = _items.indexWhere((item) => item.id == id);
    if (index != -1) {
      _items[index] = updatedItem;
      await _saveItems();
      notifyListeners();
    }
  }

  Future<void> deleteItem(String id) async {
    _items.removeWhere((item) => item.id == id);
    await _saveItems();
    notifyListeners();
  }

  List<Item> searchItems(String query) {
    if (query.isEmpty) return _items;
    return _items.where((item) {
      return item.title.toLowerCase().contains(query.toLowerCase()) ||
          item.description.toLowerCase().contains(query.toLowerCase()) ||
          item.category.toLowerCase().contains(query.toLowerCase());
    }).toList();
  }

  List<Item> filterByCategory(String category) {
    if (category.isEmpty || category == 'Semua') return _items;
    return _items.where((item) => item.category == category).toList();
  }
  
  // Update userName for all items belonging to a user
  Future<void> updateUserNameInItems(String userId, String newName) async {
    bool hasChanges = false;
    for (int i = 0; i < _items.length; i++) {
      if (_items[i].userId == userId) {
        _items[i] = Item(
          id: _items[i].id,
          title: _items[i].title,
          description: _items[i].description,
          category: _items[i].category,
          condition: _items[i].condition,
          images: _items[i].images,
          userId: _items[i].userId,
          userName: newName, // Update to new name
          createdAt: _items[i].createdAt,
        );
        hasChanges = true;
      }
    }
    
    if (hasChanges) {
      await _saveItems();
      notifyListeners();
    }
  }
}
