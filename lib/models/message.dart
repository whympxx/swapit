class Message {
  final String id;
  final String chatId;
  final String senderId;
  final String senderName;
  final String receiverId;
  final String message;
  final DateTime createdAt;
  final bool isRead;

  Message({
    required this.id,
    required this.chatId,
    required this.senderId,
    required this.senderName,
    required this.receiverId,
    required this.message,
    required this.createdAt,
    this.isRead = false,
  });

  factory Message.fromJson(Map<String, dynamic> json) {
    return Message(
      id: json['id'] ?? '',
      chatId: json['chatId'] ?? '',
      senderId: json['senderId'] ?? '',
      senderName: json['senderName'] ?? '',
      receiverId: json['receiverId'] ?? '',
      message: json['message'] ?? '',
      createdAt: DateTime.parse(json['createdAt'] ?? DateTime.now().toIso8601String()),
      isRead: json['isRead'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'chatId': chatId,
      'senderId': senderId,
      'senderName': senderName,
      'receiverId': receiverId,
      'message': message,
      'createdAt': createdAt.toIso8601String(),
      'isRead': isRead,
    };
  }

  Message copyWith({bool? isRead, String? senderName}) {
    return Message(
      id: id,
      chatId: chatId,
      senderId: senderId,
      senderName: senderName ?? this.senderName,
      receiverId: receiverId,
      message: message,
      createdAt: createdAt,
      isRead: isRead ?? this.isRead,
    );
  }
}

class Chat {
  final String id;
  final String userId1;
  final String user1Name;
  final String userId2;
  final String user2Name;
  final String? lastMessage;
  final DateTime? lastMessageTime;
  final int unreadCount;

  Chat({
    required this.id,
    required this.userId1,
    required this.user1Name,
    required this.userId2,
    required this.user2Name,
    this.lastMessage,
    this.lastMessageTime,
    this.unreadCount = 0,
  });

  factory Chat.fromJson(Map<String, dynamic> json) {
    return Chat(
      id: json['id'] ?? '',
      userId1: json['userId1'] ?? '',
      user1Name: json['user1Name'] ?? '',
      userId2: json['userId2'] ?? '',
      user2Name: json['user2Name'] ?? '',
      lastMessage: json['lastMessage'],
      lastMessageTime: json['lastMessageTime'] != null
          ? DateTime.parse(json['lastMessageTime'])
          : null,
      unreadCount: json['unreadCount'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'userId1': userId1,
      'user1Name': user1Name,
      'userId2': userId2,
      'user2Name': user2Name,
      'lastMessage': lastMessage,
      'lastMessageTime': lastMessageTime?.toIso8601String(),
      'unreadCount': unreadCount,
    };
  }

  String getOtherUserId(String currentUserId) {
    return currentUserId == userId1 ? userId2 : userId1;
  }

  String getOtherUserName(String currentUserId) {
    return currentUserId == userId1 ? user2Name : user1Name;
  }

  Chat copyWith({
    String? lastMessage,
    DateTime? lastMessageTime,
    int? unreadCount,
    String? user1Name,
    String? user2Name,
  }) {
    return Chat(
      id: id,
      userId1: userId1,
      user1Name: user1Name ?? this.user1Name,
      userId2: userId2,
      user2Name: user2Name ?? this.user2Name,
      lastMessage: lastMessage ?? this.lastMessage,
      lastMessageTime: lastMessageTime ?? this.lastMessageTime,
      unreadCount: unreadCount ?? this.unreadCount,
    );
  }
}
