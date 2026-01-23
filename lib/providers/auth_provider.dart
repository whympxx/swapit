import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import 'item_provider.dart';
import 'message_provider.dart';

class AuthProvider with ChangeNotifier {
  User? _currentUser;
  bool _isLoggedIn = false;
  ItemProvider? _itemProvider;
  MessageProvider? _messageProvider;

  User? get currentUser => _currentUser;
  bool get isLoggedIn => _isLoggedIn;
  
  // Set providers to update related data when user info changes
  void setProviders(ItemProvider itemProvider, MessageProvider messageProvider) {
    _itemProvider = itemProvider;
    _messageProvider = messageProvider;
  }

  Future<bool> login(String email, String password) async {
    // Simulasi login - dalam implementasi nyata, hubungkan ke API
    await Future.delayed(const Duration(seconds: 1));
    
    if (email.isNotEmpty && password.isNotEmpty) {
      final prefs = await SharedPreferences.getInstance();
      
      // Check if user already exists, if not create new unique ID
      String userId = prefs.getString('userId') ?? DateTime.now().millisecondsSinceEpoch.toString();
      String userName = prefs.getString('userName') ?? 'Wahyu Marga Pratama';
      String userNim = prefs.getString('userNim') ?? '2141234567';
      String userPhone = prefs.getString('userPhone') ?? '+62 123 4567 890';
      String? userAddress = prefs.getString('userAddress');
      
      _currentUser = User(
        id: userId,
        name: userName,
        email: email,
        nim: userNim,
        phone: userPhone,
        address: userAddress,
      );
      _isLoggedIn = true;
      
      await prefs.setString('userId', _currentUser!.id);
      await prefs.setString('userName', _currentUser!.name);
      await prefs.setString('userEmail', _currentUser!.email);
      await prefs.setString('userNim', _currentUser!.nim);
      await prefs.setString('userPhone', _currentUser!.phone);
      if (_currentUser!.address != null) {
        await prefs.setString('userAddress', _currentUser!.address!);
      }
      
      notifyListeners();
      return true;
    }
    return false;
  }

  Future<bool> register(String name, String email, String nim, String phone, String password) async {
    // Simulasi register - dalam implementasi nyata, hubungkan ke API
    await Future.delayed(const Duration(seconds: 1));
    
    if (email.isNotEmpty && password.isNotEmpty && name.isNotEmpty) {
      // Generate unique ID for each new user
      _currentUser = User(
        id: DateTime.now().millisecondsSinceEpoch.toString(),
        name: name,
        email: email,
        nim: nim,
        phone: phone,
      );
      _isLoggedIn = true;
      
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('userId', _currentUser!.id);
      await prefs.setString('userName', _currentUser!.name);
      await prefs.setString('userEmail', _currentUser!.email);
      await prefs.setString('userNim', _currentUser!.nim);
      await prefs.setString('userPhone', _currentUser!.phone);
      await prefs.setString('userPassword', password);
      
      notifyListeners();
      return true;
    }
    return false;
  }

  Future<void> logout() async {
    _currentUser = null;
    _isLoggedIn = false;
    
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    
    notifyListeners();
  }

  Future<void> checkLoginStatus() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getString('userId');
    
    if (userId != null) {
      _currentUser = User(
        id: userId,
        name: prefs.getString('userName') ?? '',
        email: prefs.getString('userEmail') ?? '',
        nim: prefs.getString('userNim') ?? '2141234567',
        phone: prefs.getString('userPhone') ?? '+62 123 4567 890',
        address: prefs.getString('userAddress'),
      );
      _isLoggedIn = true;
      notifyListeners();
    }
  }

  Future<void> updateUserProfile({
    String? name,
    String? phone,
    String? address,
  }) async {
    if (_currentUser != null) {
      final oldName = _currentUser!.name;
      final newName = name ?? _currentUser!.name;
      
      _currentUser = User(
        id: _currentUser!.id,
        name: newName,
        email: _currentUser!.email,
        nim: _currentUser!.nim,
        phone: phone ?? _currentUser!.phone,
        address: address ?? _currentUser!.address,
      );
      
      // Save to SharedPreferences
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('userName', _currentUser!.name);
      await prefs.setString('userPhone', _currentUser!.phone);
      if (_currentUser!.address != null) {
        await prefs.setString('userAddress', _currentUser!.address!);
      }
      
      // Update userName in all items if name changed
      if (oldName != newName && _itemProvider != null) {
        await _itemProvider!.updateUserNameInItems(_currentUser!.id, newName);
      }
      
      // Update userName in all chats if name changed
      if (oldName != newName && _messageProvider != null) {
        await _messageProvider!.updateUserNameInChats(_currentUser!.id, newName);
      }
      
      notifyListeners();
    }
  }
}
