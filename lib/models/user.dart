class User {
  final String id;
  final String name;
  final String email;
  final String nim;
  final String phone;
  final String? profileImage;
  final String? address;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.nim,
    required this.phone,
    this.profileImage,
    this.address,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      nim: json['nim'] ?? '',
      phone: json['phone'] ?? '',
      profileImage: json['profileImage'],
      address: json['address'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'nim': nim,
      'phone': phone,
      'profileImage': profileImage,
      'address': address,
    };
  }
}
