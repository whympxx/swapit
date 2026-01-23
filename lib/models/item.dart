class Item {
  final String id;
  final String title;
  final String description;
  final String category;
  final String condition;
  final List<String> images;
  final String userId;
  final String userName;
  final DateTime createdAt;
  final bool isAvailable;

  Item({
    required this.id,
    required this.title,
    required this.description,
    required this.category,
    required this.condition,
    required this.images,
    required this.userId,
    required this.userName,
    required this.createdAt,
    this.isAvailable = true,
  });

  factory Item.fromJson(Map<String, dynamic> json) {
    return Item(
      id: json['id'] ?? '',
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      category: json['category'] ?? '',
      condition: json['condition'] ?? '',
      images: List<String>.from(json['images'] ?? []),
      userId: json['userId'] ?? '',
      userName: json['userName'] ?? '',
      createdAt: DateTime.parse(json['createdAt'] ?? DateTime.now().toIso8601String()),
      isAvailable: json['isAvailable'] ?? true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'category': category,
      'condition': condition,
      'images': images,
      'userId': userId,
      'userName': userName,
      'createdAt': createdAt.toIso8601String(),
      'isAvailable': isAvailable,
    };
  }
}
