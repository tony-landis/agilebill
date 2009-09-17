<?php
class CORE_RSA
{

	var $primes;
	var $maxprimes;

	function SecurityRSA($show_debug=0) { 
		mt_srand((double)microtime()*1000000); 
		$this->primes = array (4507, 4513, 4517, 4519, 4523, 4547, 4549, 4561, 4567, 4583, 4591, 4597,
		4603, 4621, 4637, 4639, 4643, 4649, 4651, 4657, 4663, 4673, 4679, 4691, 4703, 4721, 4723, 4729, 4733, 4751,
		4759, 4783, 4787, 4789, 4793, 4799, 4801, 4813, 4817, 4831, 4861, 4871, 4877, 4889, 4903, 4909, 4919, 4931,
		4933, 4937, 4943, 4951, 4957, 4967, 4969, 4973, 4987, 4993, 4999, 5003, 5009, 5011, 5021, 5023, 5039, 5051,
		5059, 5077, 5081, 5087, 5099, 5101, 5107, 5113, 5119, 5147, 5153, 5167, 5171, 5179, 5189, 5197, 5209, 5227,
		5231, 5233, 5237, 5261, 5273, 5279, 5281, 5297, 5303, 5309, 5323, 5333, 5347, 5351, 5381, 5387, 5393, 5399,
		5407, 5413, 5417, 5419, 5431, 5437, 5441, 5443, 5449, 5471, 5477, 5479, 5483, 5501, 5503, 5507, 5519, 5521,
		5527, 5531, 5557, 5563, 5569, 5573, 5581, 5591, 5623, 5639, 5641, 5647, 5651, 5653, 5657, 5659, 5669, 5683,
		5689, 5693, 5701, 5711, 5717, 5737, 5741, 5743, 5749, 5779, 5783, 5791, 5801, 5807, 5813, 5821, 5827, 5839,
		5843, 5849, 5851, 5857, 5861, 5867, 5869, 5879, 5881, 5897, 5903, 5923, 5927, 5939, 5953, 5981, 5987, 6007,
		6011, 6029, 6037, 6043, 6047, 6053, 6067, 6073, 6079, 6089, 6091, 6101, 6113, 6121, 6131, 6133, 6143, 6151,
		6163, 6173, 6197, 6199, 6203, 6211, 6217, 6221, 6229, 6247, 6257, 6263, 6269, 6271, 6277, 6287, 6299, 6301,
		6311, 6317, 6323, 6329, 6337, 6343, 6353, 6359, 6361, 6367, 6373, 6379, 6389, 6397, 6421, 6427, 6449, 6451,
		6469, 6473, 6481, 6491, 6521, 6529, 6547, 6551, 6553, 6563, 6569, 6571, 6577, 6581, 6599, 6607, 6619, 6637,
		6653, 6659, 6661, 6673, 6679, 6689, 6691, 6701, 6703, 6709, 6719, 6733, 6737, 6761, 6763, 6779, 6781, 6791,
		6793, 6803, 6823, 6827, 6829, 6833, 6841, 6857, 6863, 6869, 6871, 6883, 6899, 6907, 6911, 6917, 6947, 6949,
		6959, 6961, 6967, 6971, 6977, 6983, 6991, 6997, 7001, 7013, 7019, 7027, 7039, 7043, 7057, 7069, 7079, 7103,
		7109, 7121, 7127, 7129, 7151, 7159, 7177, 7187, 7193, 7207, 7211, 7213, 7219, 7229, 7237, 7243, 7247, 7253,
		7283, 7297, 7307, 7309, 7321, 7331, 7333, 7349, 7351, 7369, 7393, 7411, 7417, 7433, 7451, 7457, 7459, 7477,
		7481, 7487, 7489, 7499, 7507, 7517, 7523, 7529, 7537, 7541, 7547, 7549, 7559, 7561, 7573, 7577, 7583, 7589,
		7591, 7603, 7607, 7621, 7639, 7643, 7649, 7669, 7673, 7681, 7687, 7691, 7699, 7703, 7717, 7723, 7727, 7741,
		7753, 7757, 7759, 7789, 7793, 7817, 7823, 7829, 7841, 7853, 7867, 7873, 7877, 7879, 7883, 7901, 7907, 7919,
		7927, 7933, 7937, 7949, 7951, 7963, 7993, 8009, 8011, 8017, 8039, 8053, 8059, 8069, 8081, 8087, 8089, 8093,
		8101, 8111, 8117, 8123, 8147, 8161, 8167, 8171, 8179, 8191, 8209, 8219, 8221, 8231, 8233, 8237, 8243, 8263,
		8269, 8273, 8287, 8291, 8293, 8297, 8311, 8317, 8329, 8353, 8363, 8369, 8377, 8387, 8389, 8419, 8423, 8429,
		8431, 8443, 8447, 8461, 8467, 8501, 8513, 8521, 8527, 8537, 8539, 8543, 8563, 8573, 8581, 8597, 8599, 8609,
		8623, 8627, 8629, 8641, 8647, 8663, 8669, 8677, 8681, 8689, 8693, 8699, 8707, 8713, 8719, 8731, 8737, 8741,
		8747, 8753, 8761, 8779, 8783, 8803, 8807, 8819, 8821, 8831, 8837, 8839, 8849, 8861, 8863, 8867, 8887, 8893,
		8923, 8929, 8933, 8941, 8951, 8963, 8969, 8971, 8999, 9001, 9007, 9011, 9013, 9029, 9041, 9043, 9049, 9059,
		9067, 9091, 9103, 9109, 9127, 9133, 9137, 9151, 9157, 9161, 9173, 9181, 9187, 9199, 9203, 9209, 9221, 9227,
		9239, 9241, 9257, 9277, 9281, 9283, 9293, 9311, 9319, 9323, 9337, 9341, 9343, 9349, 9371, 9377, 9391, 9397,
		9403, 9413, 9419, 9421, 9431, 9433, 9437, 9439, 9461, 9463, 9467, 9473, 9479, 9491, 9497, 9511, 9521, 9533);			
		$this->maxprimes = count($this->primes) - 1;
	}

	function generate_keys($show_debug=0){ 
		while (empty($e) || empty($d)) { 
			$p = $this->primes[mt_rand(0, $this->maxprimes)];
			while (empty($q) || ($p==$q)) {
				$q = $this->primes[mt_rand(0, $this->maxprimes)];
			} 
			$n = $p*$q; 
			$pi = ($p - 1) * ($q - 1); 
			$e = $this->tofindE($pi, $p, $q); 
			$d = $this->extend($e,$pi); 
			$keys = array ($n, $e, $d);
		}
		return $keys;
	} 
	function mo($g, $l) {
		return $g - ($l * floor ($g/$l));
	}

	function extend($Ee,$Epi) {
		$u1 = 1;
		$u2 = 0;
		$u3 = $Epi;
		$v1 = 0;
		$v2 = 1;
		$v3 = $Ee;
		while ($v3 != 0) {
			$qq = floor($u3/$v3);
			$t1 = $u1 - $qq * $v1;
			$t2 = $u2 - $qq * $v2;
			$t3 = $u3 - $qq * $v3;
			$u1 = $v1;
			$u2 = $v2;
			$u3 = $v3;
			$v1 = $t1;
			$v2 = $t2;
			$v3 = $t3;
			$z = 1;
		}
		$uu = $u1;
		$vv = $u2;
		if ($vv < 0) {
			$inverse = $vv + $Epi;
		} else {
			$inverse = $vv;
		}
		return $inverse;
	}

	function GCD($e,$pi) {
		$y = $e;
		$x = $pi;
		while ($y != 0) {
			$w =  $this->mo($x , $y);
			$x = $y;
			$y = $w;
		}
		return $x;
	}

	function tofindE($pi) { 
		$great = 0;
		$cc = mt_rand (0,$this->maxprimes);
		$startcc = $cc;
		while ($cc >= 0) {
			$se = $this->primes[$cc];
			$great = $this->GCD($se,$pi);
			$cc--;
			if ($great == 1) break;
		}
		if ($great == 0) {
			$cc = $startcc + 1;
			while ($cc <= $this->maxprimes) {
				$se = $this->primes[$cc];
				$great = $this->GCD($se,$pi);
				$cc++;
				if ($great == 1) break;
			}
		}
		return $se;
	}

	function rsa_encrypt($m, $e, $n) {
		$asci = array ();
		for ($i=0; $i<strlen($m); $i+=3) {
			$tmpasci="1";
			for ($h=0; $h<3; $h++) {
				if ($i+$h <strlen($m)) {
					$tmpstr = ord (substr ($m, $i+$h, 1)) - 30;
					if (strlen($tmpstr) < 2) {
						$tmpstr ="0".$tmpstr;
					}
				} else {
					break;
				}
				$tmpasci .=$tmpstr;
			}
			array_push($asci, $tmpasci."1");
		}

		for ($k=0; $k< count ($asci); $k++) {
			$resultmod = $this->powmod($asci[$k], $e, $n);
			@$coded .= $resultmod." ";
		}
		return trim(@$coded);
	}

	function powmod($base, $exp, $modulus) {
		$accum = 1;
		$i = 0;
		$basepow2 = $base;
		while (($exp >> $i)>0) {
			if ((($exp >> $i) & 1) == 1) {
				$accum = $this->mo(($accum * $basepow2) , $modulus);
			}
			$basepow2 = $this->mo(($basepow2 * $basepow2) , $modulus);
			$i++;
		}
		return $accum;
	}

	function rsa_decrypt($c, $d, $n) { 
		$decryptarray = explode(" ", $c);
		for ($u=0; $u<count ($decryptarray); $u++) {
			if ($decryptarray[$u] == "") {
				array_splice($decryptarray, $u, 1);
			}
		} 
		for ($u=0; $u< count($decryptarray); $u++) {
			$resultmod = $this->powmod($decryptarray[$u], $d, $n); 
			@$deencrypt.= substr ($resultmod,1,strlen($resultmod)-2);
		} 
		for ($u=0; $u<strlen($deencrypt); $u+=2) {
			@$resultd .= chr(substr ($deencrypt, $u, 2) + 30);
		}
		return @$resultd;
	}
}  



function CORE_encrypt($data)  {
	if(LICENSE_KEY == '') return $data; // provide a license key in the setup area to enable encryption
	$rsa 		= new CORE_RSA;
	$keys 		= explode('-', LICENSE_KEY); 
	$rsa_data   = $rsa->rsa_encrypt($data, $keys[1], $keys[0]);  
	$rc4_key	= do_rc4(LICENSE_KEY, 'en', false);    
	$rc4_data 	= do_rc4($rsa_data, 'en', $rc4_key); 
	return $rc4_data;
}


function CORE_decrypt($data)  {
	if(LICENSE_KEY == '') return $data;  // provide a license key in the setup area to enable encryption
	$rc4_key	= do_rc4(LICENSE_KEY, 'en', false);    
	$rc4_data 	= do_rc4($data, 'de', $rc4_key);  
	$rsa 		= new CORE_RSA;
	$keys 		= explode('-', LICENSE_KEY); 
	$rsa_data   = $rsa->rsa_decrypt($rc4_data, $keys[2], $keys[0]); 	
	return $rsa_data;
}


function do_rc4($data, $case, $pwd) {  
	if(!$pwd) $pwd = '21e0*kO-(uV9B0@jFk-er'; 
	if ($case == 'de') {
		$data = urldecode($data);
	}
	$key[] = "";
	$box[] = "";
	$temp_swap = "";
	$pwd_length = 0;
	$pwd_length = strlen($pwd); 
	for ($i = 0; $i <= 255; $i++) {
		$key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
		$box[$i] = $i;
	}
	$x = 0; 
	for ($i = 0; $i <= 255; $i++) {
		$x = ($x + $box[$i] + $key[$i]) % 256;
		$temp_swap = $box[$i];
		$box[$i] = $box[$x];
		$box[$x] = $temp_swap;
	}
	$temp = "";
	$k = "";
	$cipherby = "";
	$cipher = "";
	$a = 0;
	$j = 0;
	for ($i = 0; $i < strlen($data); $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$temp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $temp;
		$k = $box[(($box[$a] + $box[$j]) % 256)];
		$cipherby = ord(substr($data, $i, 1)) ^ $k;
		$cipher .= chr($cipherby);
	}
	if ($case == 'de') {
		$cipher = urldecode(urlencode($cipher));
	} else {
		$cipher = urlencode($cipher);
	}
	return $cipher;
} 
?>