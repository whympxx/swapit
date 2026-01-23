import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../providers/message_provider.dart';
import '../providers/auth_provider.dart';

class ListPesanScreen extends StatelessWidget {
  const ListPesanScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final currentUserId = authProvider.currentUser?.id ?? '';

    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: const Text('Pesan'),
        backgroundColor: const Color(0xFF5B4DFF),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Consumer<MessageProvider>(
        builder: (context, messageProvider, _) {
          final chats = messageProvider.getChatsForUser(currentUserId);

          if (chats.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.chat_bubble_outline,
                    size: 80,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Belum ada percakapan',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w500,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Mulai chat dengan pengguna lain',
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey[500],
                    ),
                  ),
                ],
              ),
            );
          }

          return ListView.separated(
            itemCount: chats.length,
            separatorBuilder: (context, index) => Divider(
              height: 1,
              indent: 80,
              color: Colors.grey[200],
            ),
            itemBuilder: (context, index) {
              final chat = chats[index];
              final otherUserName = chat.getOtherUserName(currentUserId);
              final otherUserId = chat.getOtherUserId(currentUserId);
              final unreadCount = messageProvider.getUnreadCountForChat(chat.id, currentUserId);
              
              return InkWell(
                onTap: () {
                  Navigator.pushNamed(
                    context,
                    '/pesan',
                    arguments: {
                      'chatId': chat.id,
                      'otherUserId': otherUserId,
                      'otherUserName': otherUserName,
                    },
                  );
                },
                child: Container(
                  color: unreadCount > 0 ? const Color(0xFF5B4DFF).withOpacity(0.05) : Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  child: Row(
                    children: [
                      // Avatar
                      Stack(
                        children: [
                          CircleAvatar(
                            radius: 28,
                            backgroundColor: const Color(0xFF5B4DFF),
                            child: Text(
                              otherUserName.isNotEmpty ? otherUserName[0].toUpperCase() : 'U',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          if (unreadCount > 0)
                            Positioned(
                              right: 0,
                              top: 0,
                              child: Container(
                                padding: const EdgeInsets.all(4),
                                decoration: BoxDecoration(
                                  color: Colors.red,
                                  shape: BoxShape.circle,
                                  border: Border.all(color: Colors.white, width: 2),
                                ),
                                constraints: const BoxConstraints(
                                  minWidth: 20,
                                  minHeight: 20,
                                ),
                                child: Center(
                                  child: Text(
                                    unreadCount > 9 ? '9+' : unreadCount.toString(),
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(width: 16),
                      // Chat Info
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Expanded(
                                  child: Text(
                                    otherUserName,
                                    style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: unreadCount > 0 ? FontWeight.bold : FontWeight.w600,
                                      color: Colors.black87,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ),
                                if (chat.lastMessageTime != null)
                                  Text(
                                    _formatTime(chat.lastMessageTime!),
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: unreadCount > 0 ? const Color(0xFF5B4DFF) : Colors.grey[600],
                                      fontWeight: unreadCount > 0 ? FontWeight.bold : FontWeight.normal,
                                    ),
                                  ),
                              ],
                            ),
                            const SizedBox(height: 4),
                            Text(
                              chat.lastMessage ?? 'Belum ada pesan',
                              style: TextStyle(
                                fontSize: 14,
                                color: unreadCount > 0 ? Colors.black87 : Colors.grey[600],
                                fontWeight: unreadCount > 0 ? FontWeight.w500 : FontWeight.normal,
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                      // Unread indicator
                      if (unreadCount > 0)
                        const SizedBox(
                          width: 8,
                          height: 8,
                          child: DecoratedBox(
                            decoration: BoxDecoration(
                              color: Color(0xFF5B4DFF),
                              shape: BoxShape.circle,
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }

  String _formatTime(DateTime time) {
    final now = DateTime.now();
    final difference = now.difference(time);

    if (difference.inDays == 0) {
      return DateFormat('HH:mm').format(time);
    } else if (difference.inDays == 1) {
      return 'Kemarin';
    } else if (difference.inDays < 7) {
      return DateFormat('EEEE').format(time);
    } else {
      return DateFormat('dd/MM/yy').format(time);
    }
  }
}
