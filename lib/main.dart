import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
import 'screens/home_screen.dart';
import 'screens/profile_screen.dart';
import 'screens/item_detail_screen.dart';
import 'screens/posting_screen.dart';
import 'screens/penawaran_screen.dart';
import 'screens/buat_penawaran_screen.dart';
import 'screens/barang_saya_screen.dart';
import 'screens/pesan_screen.dart';
import 'screens/list_pesan_screen.dart';
import 'screens/pengaturan_screen.dart';
import 'screens/offer_detail_screen.dart';
import 'providers/auth_provider.dart';
import 'providers/item_provider.dart';
import 'providers/offer_provider.dart';
import 'providers/message_provider.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => ItemProvider()),
        ChangeNotifierProvider(create: (_) => MessageProvider()),
        ChangeNotifierProvider(create: (_) => OfferProvider()),
        ChangeNotifierProxyProvider2<ItemProvider, MessageProvider, AuthProvider>(
          create: (_) => AuthProvider(),
          update: (_, itemProvider, messageProvider, authProvider) {
            authProvider?.setProviders(itemProvider, messageProvider);
            return authProvider ?? AuthProvider();
          },
        ),
      ],
      child: MaterialApp(
        title: 'Swap It',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          primaryColor: const Color(0xFF5B4DFF),
          colorScheme: ColorScheme.fromSeed(
            seedColor: const Color(0xFF5B4DFF),
            primary: const Color(0xFF5B4DFF),
          ),
          fontFamily: 'Roboto',
          useMaterial3: true,
        ),
        initialRoute: '/login',
        routes: {
          '/login': (context) => const LoginScreen(),
          '/register': (context) => const RegisterScreen(),
          '/home': (context) => const HomeScreen(),
          '/profile': (context) => const ProfileScreen(),
          '/item-detail': (context) => const ItemDetailScreen(),
          '/posting': (context) => const PostingScreen(),
          '/penawaran': (context) => const PenawaranScreen(),
          '/buat-penawaran': (context) => const BuatPenawaranScreen(),
          '/barang-saya': (context) => const BarangSayaScreen(),
          '/pesan': (context) => const PesanScreen(),
          '/list-pesan': (context) => const ListPesanScreen(),
          '/pengaturan': (context) => const PengaturanScreen(),
          '/offer-detail': (context) => const OfferDetailScreen(),
        },
      ),
    );
  }
}
