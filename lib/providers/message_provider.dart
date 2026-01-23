import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/message.dart';

class MessageProvider with ChangeNotifier {
  List<Chat> _chats = [];
  List<Message> _messages = [];
  bool _isLoaded = false;

  MessageProvider() {
    _loadData();
  }

  List<Chat> get chats => _chats;
  List<Message> get messages => _messages;
  bool get isLoaded => _isLoaded;

  // Load data from SharedPreferences
  Future<void> _loadData() async {
    if (_isLoaded) return;

    final prefs = await SharedPreferences.getInstance();

    // Load chats
    final String? chatsJson = prefs.getString('chats');
    if (chatsJson != null && chatsJson.isNotEmpty) {
      try {
        final List<dynamic> decoded = json.decode(chatsJson);
        _chats = decoded.map((chat) => Chat.fromJson(chat)).toList();
      } catch (e) {
        print('Error loading chats: $e');
        _chats = [];
      }
    }

    // Load messages
    final String? messagesJson = prefs.getString('messages');
    if (messagesJson != null && messagesJson.isNotEmpty) {
      try {
        final List<dynamic> decoded = json.decode(messagesJson);
        _messages = decoded.map((msg) => Message.fromJson(msg)).toList();
      } catch (e) {
        print('Error loading messages: $e');
        _messages = [];
      }
    }

    _isLoaded = true;
    notifyListeners();
  }

  // Save chats
  Future<void> _saveChats() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final String chatsJson = json.encode(_chats.map((chat) => chat.toJson()).toList());
      await prefs.setString('chats', chatsJson);
    } catch (e) {
      print('Error saving chats: $e');
    }
  }

  // Save messages
  Future<void> _saveMessages() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final String messagesJson = json.encode(_messages.map((msg) => msg.toJson()).toList());
      await prefs.setString('messages', messagesJson);
    } catch (e) {
      print('Error saving messages: $e');
    }
  }

  // Get or create chat between two users
  Future<Chat> getOrCreateChat(String userId1, String userName1, String userId2, String userName2) async {
    // Check if chat already exists
    final existingChat = _chats.firstWhere(
      (chat) =>
          (chat.userId1 == userId1 && chat.userId2 == userId2) ||
          (chat.userId1 == userId2 && chat.userId2 == userId1),
      orElse: () => Chat(
        id: '',
        userId1: '',
        user1Name: '',
        userId2: '',
        user2Name: '',
      ),
    );

    if (existingChat.id.isNotEmpty) {
      return existingChat;
    }

    // Create new chat
    final newChat = Chat(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      userId1: userId1,
      user1Name: userName1,
      userId2: userId2,
      user2Name: userName2,
      lastMessage: null,
      lastMessageTime: null,
      unreadCount: 0,
    );

    _chats.insert(0, newChat);
    await _saveChats();
    notifyListeners();

    return newChat;
  }

  // Get chats for user
  List<Chat> getChatsForUser(String userId) {
    return _chats
        .where((chat) => chat.userId1 == userId || chat.userId2 == userId)
        .toList()
      ..sort((a, b) {
        if (a.lastMessageTime == null) return 1;
        if (b.lastMessageTime == null) return -1;
        return b.lastMessageTime!.compareTo(a.lastMessageTime!);
      });
  }

  // Get messages for chat
  List<Message> getMessagesForChat(String chatId) {
    return _messages
        .where((msg) => msg.chatId == chatId)
        .toList()
      ..sort((a, b) => a.createdAt.compareTo(b.createdAt));
  }

  // Send message
  Future<void> sendMessage({
    required String chatId,
    required String senderId,
    required String senderName,
    required String receiverId,
    required String messageText,
  }) async {
    final newMessage = Message(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      chatId: chatId,
      senderId: senderId,
      senderName: senderName,
      receiverId: receiverId,
      message: messageText,
      createdAt: DateTime.now(),
      isRead: false,
    );

    _messages.add(newMessage);
    await _saveMessages();

    // Update chat
    final chatIndex = _chats.indexWhere((chat) => chat.id == chatId);
    if (chatIndex != -1) {
      final updatedChat = _chats[chatIndex].copyWith(
        lastMessage: messageText,
        lastMessageTime: DateTime.now(),
      );
      _chats[chatIndex] = updatedChat;

      // Move chat to top
      _chats.removeAt(chatIndex);
      _chats.insert(0, updatedChat);

      await _saveChats();
    }

    notifyListeners();
  }

  // Mark messages as read
  Future<void> markMessagesAsRead(String chatId, String userId) async {
    bool hasChanges = false;

    for (int i = 0; i < _messages.length; i++) {
      if (_messages[i].chatId == chatId &&
          _messages[i].receiverId == userId &&
          !_messages[i].isRead) {
        _messages[i] = _messages[i].copyWith(isRead: true);
        hasChanges = true;
      }
    }

    if (hasChanges) {
      await _saveMessages();

      // Update unread count in chat
      final chatIndex = _chats.indexWhere((chat) => chat.id == chatId);
      if (chatIndex != -1) {
        _chats[chatIndex] = _chats[chatIndex].copyWith(unreadCount: 0);
        await _saveChats();
      }

      notifyListeners();
    }
  }

  // Get unread count for user
  int getUnreadCountForUser(String userId) {
    int count = 0;
    for (var message in _messages) {
      if (message.receiverId == userId && !message.isRead) {
        count++;
      }
    }
    return count;
  }

  // Get unread count for chat
  int getUnreadCountForChat(String chatId, String userId) {
    return _messages
        .where((msg) =>
            msg.chatId == chatId && msg.receiverId == userId && !msg.isRead)
        .length;
  }
  
  // Update userName in all chats and messages
  Future<void> updateUserNameInChats(String userId, String newName) async {
    bool hasChanges = false;
    
    // Update chats
    for (int i = 0; i < _chats.length; i++) {
      if (_chats[i].userId1 == userId) {
        _chats[i] = _chats[i].copyWith(user1Name: newName);
        hasChanges = true;
      } else if (_chats[i].userId2 == userId) {
        _chats[i] = _chats[i].copyWith(user2Name: newName);
        hasChanges = true;
      }
    }
    
    if (hasChanges) {
      await _saveChats();
    }
    
    // Update messages
    hasChanges = false;
    for (int i = 0; i < _messages.length; i++) {
      if (_messages[i].senderId == userId) {
        _messages[i] = _messages[i].copyWith(senderName: newName);
        hasChanges = true;
      }
    }
    
    if (hasChanges) {
      await _saveMessages();
    }
    
    notifyListeners();
  }
}
