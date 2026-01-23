import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/offer.dart';

class OfferProvider with ChangeNotifier {
  List<Offer> _offers = [];
  bool _isLoaded = false;

  OfferProvider() {
    _loadOffers();
  }

  // Load offers from SharedPreferences
  Future<void> _loadOffers() async {
    if (_isLoaded) return;
    
    final prefs = await SharedPreferences.getInstance();
    final String? offersJson = prefs.getString('offers');
    
    if (offersJson != null && offersJson.isNotEmpty) {
      try {
        final List<dynamic> decoded = json.decode(offersJson);
        _offers = decoded.map((offer) => Offer.fromJson(offer)).toList();
      } catch (e) {
        print('Error loading offers: $e');
        _offers = [];
      }
    }
    
    _isLoaded = true;
    notifyListeners();
  }

  // Save offers to SharedPreferences
  Future<void> _saveOffers() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final String offersJson = json.encode(_offers.map((offer) => offer.toJson()).toList());
      await prefs.setString('offers', offersJson);
    } catch (e) {
      print('Error saving offers: $e');
    }
  }

  List<Offer> get offers => _offers;

  // Get offers sent by user
  List<Offer> getSentOffers(String userId) {
    return _offers.where((offer) => offer.fromUserId == userId).toList()
      ..sort((a, b) => b.createdAt.compareTo(a.createdAt));
  }

  // Get offers received by user
  List<Offer> getReceivedOffers(String userId) {
    return _offers.where((offer) => offer.toUserId == userId).toList()
      ..sort((a, b) => b.createdAt.compareTo(a.createdAt));
  }

  // Get offers by status
  List<Offer> getOffersByStatus(String userId, String status, {bool isSent = true}) {
    final offers = isSent ? getSentOffers(userId) : getReceivedOffers(userId);
    if (status == 'all') return offers;
    return offers.where((offer) => offer.status == status).toList();
  }

  // Add new offer
  Future<void> addOffer(Offer offer) async {
    _offers.add(offer);
    await _saveOffers();
    notifyListeners();
  }

  // Accept offer
  Future<void> acceptOffer(String offerId) async {
    final index = _offers.indexWhere((offer) => offer.id == offerId);
    if (index != -1) {
      final offer = _offers[index];
      _offers[index] = Offer(
        id: offer.id,
        itemId: offer.itemId,
        offeredItemId: offer.offeredItemId,
        fromUserId: offer.fromUserId,
        toUserId: offer.toUserId,
        message: offer.message,
        status: 'accepted',
        createdAt: offer.createdAt,
      );
      await _saveOffers();
      notifyListeners();
    }
  }

  // Reject offer
  Future<void> rejectOffer(String offerId) async {
    final index = _offers.indexWhere((offer) => offer.id == offerId);
    if (index != -1) {
      final offer = _offers[index];
      _offers[index] = Offer(
        id: offer.id,
        itemId: offer.itemId,
        offeredItemId: offer.offeredItemId,
        fromUserId: offer.fromUserId,
        toUserId: offer.toUserId,
        message: offer.message,
        status: 'rejected',
        createdAt: offer.createdAt,
      );
      await _saveOffers();
      notifyListeners();
    }
  }

  // Cancel offer
  Future<void> cancelOffer(String offerId) async {
    final index = _offers.indexWhere((offer) => offer.id == offerId);
    if (index != -1) {
      final offer = _offers[index];
      _offers[index] = Offer(
        id: offer.id,
        itemId: offer.itemId,
        offeredItemId: offer.offeredItemId,
        fromUserId: offer.fromUserId,
        toUserId: offer.toUserId,
        message: offer.message,
        status: 'cancelled',
        createdAt: offer.createdAt,
      );
      await _saveOffers();
      notifyListeners();
    }
  }

  // Get offer by ID
  Offer? getOfferById(String id) {
    try {
      return _offers.firstWhere((offer) => offer.id == id);
    } catch (e) {
      return null;
    }
  }

  // Count offers by status
  int countOffersByStatus(String userId, String status, {bool isSent = true}) {
    return getOffersByStatus(userId, status, isSent: isSent).length;
  }
}
