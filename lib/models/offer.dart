class Offer {
  final String id;
  final String itemId;
  final String offeredItemId;
  final String fromUserId;
  final String toUserId;
  final String message;
  final String status; // pending, accepted, rejected
  final DateTime createdAt;

  Offer({
    required this.id,
    required this.itemId,
    required this.offeredItemId,
    required this.fromUserId,
    required this.toUserId,
    required this.message,
    this.status = 'pending',
    required this.createdAt,
  });

  factory Offer.fromJson(Map<String, dynamic> json) {
    return Offer(
      id: json['id'] ?? '',
      itemId: json['itemId'] ?? '',
      offeredItemId: json['offeredItemId'] ?? '',
      fromUserId: json['fromUserId'] ?? '',
      toUserId: json['toUserId'] ?? '',
      message: json['message'] ?? '',
      status: json['status'] ?? 'pending',
      createdAt: DateTime.parse(json['createdAt'] ?? DateTime.now().toIso8601String()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'itemId': itemId,
      'offeredItemId': offeredItemId,
      'fromUserId': fromUserId,
      'toUserId': toUserId,
      'message': message,
      'status': status,
      'createdAt': createdAt.toIso8601String(),
    };
  }
}
