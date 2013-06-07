<?php defined('BASEPATH') or exit('No direct script access allowed');

// labels
$lang['header']			=	'Βήμα 2: Έλεγχος Απαιτήσεων';
$lang['intro_text']		= 	'Το πρώτο βήμα της εγκατάστασης είναι να ελέγχθεί αν ο διακομιστής σας υποστηρίζει το PyroCMS. Οι περισσότεροι διακομιστές θα μπορούν να το τρέξουν χωρίς προβλήματα.';
$lang['mandatory']		= 	'Απαιτούμενο';
$lang['recommended']	= 	'Συνιστόμενο';

$lang['server_settings']= 	'Ρυθμίσεις Διακομιστή HTTP';
$lang['server_version']	=	'Το λογισμικό του διακομιστή σας:';
$lang['server_fail']	=	'Το λογισμικό του διακομιστή σας δεν υποστηρίζεται, έτσι το PyroCMS μπορεί να δουλέψει ή και όχι. Εφόσον οι εκδόσεις των PHP και MySQL είναι ενημερωμένες το PyroCMS θα πρέπει να μπορεί να τρέξει κανονικά, απλώς χωρίς clean URL\'s.';

$lang['php_settings']	=	'Ρυθμίσεις PHP';
$lang['php_required']	=	'Το PyroCMS απαιτεί PHP εκδόσης %s ή πιο πρόσφατη.';
$lang['php_version']	=	'Ο διακομιστής σας έχει την έκδοση';
$lang['php_fail']		=	'Η τρέχουσα έκδοση της PHP δεν υποστηρίζεται. Το PyroCMS απαιτεί PHP έκδοσης %s ή πιο πρόσφατη για να μπορεί να δουλέψει κανονικά.';

$lang['mysql_settings']	=	'Ρυθμίσεις MySQL';
$lang['mysql_required']	=	'Το PyroCMS απαιτεί πρόσβαση σε μια βάση δεδομένων που βρίσκεται σε διακομιστή MySQL έκδοσης 5.0 ή πιο πρόσφατη.';
$lang['mysql_version1']	=	'Ο διακομιστής σας εκτελείτε';
$lang['mysql_version2']	=	'Το πρόγραμμα πελάτης εκτελείτε';
$lang['mysql_fail']		=	'Η έκδοση του διακομιστή MySQL σας δεν υποστηρίζεται. Το PyroCMS απαιτεί διακομιστή MySQL έκδοσης 5.0 ή πιο πρόσφατης για να δουλέψει κανονικά.';

$lang['gd_settings']	=	'Ρυθμίσεις GD';
$lang['gd_required']	= 	'Το PyroCMS απαιτεί την βιβλιοθήκη GD έκδοση 1.0 ή πιο πρόσφατη για να μεταχειριστεί εικόνες.';
$lang['gd_version']		= 	'Ο διακομιστής σας έχει την έκδοση';
$lang['gd_fail']		=	'Δεν μπορέσαμε να διευκρινίσουμε την έκδοση της βιβλιοθήκης GD. Αυτό συνήθως σημαίνει ότι η βιβλιοθήκη GD δεν είναι εγκατεστημένη. Το PyroCMS θα μπορεί να λειτουργήσει αλλά μερικές λειτουργίες που έχουν να κάνουν με εικόνες μπορεί να μην μπορούν να ολοκληρωθούν. Συνίσταται να ενεργοποιήσετε την βιβλιοθήκη GD.';

$lang['summary']		=	'Περίληψη';

$lang['zlib']			=	'Zlib';
$lang['zlib_required']	= 	'Το PyroCMS απαιτεί την Zlib για να μπορεί να αποσυμπιέζει και να εγκαθιστά τα θέματα.';
$lang['zlib_fail']		=	'Η Zlib δεν βρέθηκε. Συνήθως, αυτό σημαίνει ότι η Zlib δεν είναι εγκατεστημένη. Το PyroCMS θα μπορεί να λειτουργήσει κανονικά αλλά η εγκατάσταση των θεμάτων δεν θα δουλεύει. Συνίσταται να εγκαταστήσετε την Zlib.';

$lang['curl']			=	'Curl';
$lang['curl_required']	=	'Το PyroCMS απαιτεί την Curl για να μπορεί να συνδέεται σε άλλους ιστοτόπους.';
$lang['curl_fail']		=	'Η Curl δεν βρέθηκε. Συνήθως, αυτό σημαίνει ότι η Curl δεν είναι εγκατεστημένη. Το PyroCMS θα μπορεί να λειτουργήσει κανονικά αλλά μερικές από τις λειτουργίες μπορεί να μην μπορούν να εκτελεστούν. Συνίσταται να εγκαταστήσετε την βιβλιοθήκη Curl.';

$lang['summary_success']	=	'Ο διακομιστής σας, πληρoί τις απαιτήσεις για να μπορεί το PyroCMS να εκτελεστεί κανονικά, πηγαίνετε στο επόμενο βήμα κάνοντας κλικ παρακάτω.';
$lang['summary_partial']	=	'Ο διακομιστής σας, πληροί <em>τις περισσότερες</em> από τις απαιτήσεις του PyroCMS. Αυτό σημαίνει ότι το PyroCMS θα πρέπει να μπορεί να εκτελεστεί κανονικά αλλά υπάρχουν πιθανότητες προβλημάτων κατά την προσαρμογή διαστάσεων εικονών και την δημιουργία μικρογραφιών.';
$lang['summary_failure']	=	'Φαίνεται ότι ο διακομιστής σας δεν πληρεί τις απαιτήσεις για να εκτελεστεί το PyroCMS. Παρακαλούμε επικοινωνήστε με τον διαχειριστή ή την εταιρία web hosting για να βρούν λύση.';
$lang['next_step']		=	'Προχωρήστε στο επόμενο βήμα';
$lang['step3']			=	'Βήμα 3';
$lang['retry']			=	'Προσπαθήστε ξανά';

// messages
$lang['step1_failure']	=	'Παρακαλούμε συμπληρώστε τις απαιτούμενες ρυθμίσεις για την βάση δεδομένων παρακάτω..';
