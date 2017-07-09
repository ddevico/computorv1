#!/usr/bin/php
<?php
	error_reporting(0);
	if ($argc == 1)
	{
		print_r("Veuillez saisir une équation en paramétre.\n");
	}
	else if ($argc != 2)
	{
		print_r("Veuillez saisir une équation en un seul paramétre\n");
	}
	else
	{
		$par = preg_replace('/\s{2,}/', ' ', $argv[1]);
		$par = explode('=', $par);
		$str1 = '+ ' . trim($par[0]);
		$str2 = '+ ' . trim($par[1]);

		if ($m1 == null)
			preg_match_all('/[\+\-][ ]{0,1}([\+\-]?[0-9\.]+){0,1}[ ]{0,1}[\*]{0,1}[ ]{0,1}[xX]{0,1}[ ]{0,1}[\^]{0,1}([\-]{0,1}[0-9\.]+){0,1}/s', $str1, $m1, PREG_SET_ORDER);
		if ($m2 == null)
			preg_match_all('/[\+\-][ ]{0,1}([\+\-]?[0-9\.]+){0,1}[ ]{0,1}[\*]{0,1}[ ]{0,1}[xX]{0,1}[ ]{0,1}[\^]{0,1}([\-]{0,1}[0-9\.]+){0,1}/s', $str2, $m2, PREG_SET_ORDER);
		$i = 0;
		$drap = 0;

		while ($m1[$i])
		{
			$j = 0;
			while ($m1[$i][0][$j])
			{
				if ($m1[$i][0][$j] == 'x' || $m1[$i][0][$j] == 'X')
				{
					if (!is_numeric($m1[$i][2]))
					{
						$m1[$i][2] = '1';
						$drap = 1;
					}
				}
				$j++;
			}
			if ($drap == 0)
			{
				if (!is_numeric($m1[$i][2]))
				{
					$m1[$i][2] = '0';
					$drap = 1;
				}
			}
			$drap = 0;
			$i++;
		}

		$i = 0;
		$drap = 0;
		while ($m2[$i])
		{
			$j = 0;
			while ($m2[$i][0][$j])
			{
				if ($m2[$i][0][$j] == 'x' || $m2[$i][0][$j] == 'X')
				{
					if (!is_numeric($m2[$i][2]))
					{
						$m2[$i][2] = '1';
						$drap = 1;
					}
				}
				$j++;
			}
			if ($drap == 0)
			{
				if (!is_numeric($m2[$i][2]))
				{
					$m2[$i][2] = '0';
					$drap = 1;
				}
			}
			$drap = 0;
			$i++;
		}
		if (verif_puissance($m1, $m2) == 1){
			print("Erreur puissance\n");
			exit (0);
		}
		$i = 0;
		$puissance = 0;
		while ($m1[$i])
		{
			if ($puissance < $m1[$i][2])
				$puissance = $m1[$i][2];
			$i++;
		}

    $i = 0;
		while ($m2[$i + 1] && $m2[$i])
			$i++;
		if ($m2[$i] && $m2[$i][2] > $puissance)
			$puissance = $m2[$i][2];
    if ($puissance == 0)
		{
			if (test_egalite($m1, $m2))
				exit (0);
			print("Tous les entiers sont solution !\n");
			exit (0);
		}
		if ($m1 == NULL)
		{
			print("Veuillez saisir une equation valable\n");
			exit(0);
		}

		$m1 = verif_negatif($m1);
		$m2 = verif_negatif($m2);
		$ok = concatene($m1, $m2);
		$ok = reduction($ok, $puissance);
		$i = 0;
		$puissance = 0;
		while ($ok[$i])
		{
			if ($puissance < $ok[$i][2])
				$puissance = $ok[$i][2];
			$i++;
		}

		print("Reduced form: ");
		$ok = print_equation($ok);
		if ($puissance >= 1)
		echo "Polynomial degree: ".$puissance."\n";
		if ($puissance > 2)
		{
			print_r("The polynomial degree is stricly greater than 2, I can't solve.\n");
			exit (0);
		}
		if ($puissance == 2)
		{
			if ($ok[0][2] != 0 || $ok[1][2] != 1)
				$ok = addxo($ok);
			solution_degres_2($ok);
		}
		else if ($puissance == 1)
			solution_degres_1($ok);
		else
			print_r("Impossible\n");
	}

	function reduction_fraction ($tab1, $tab2, $sol)
	{
		$i = 1;
		if ($tab1 < 0)
			$tab1 = -$tab1;
		if ($tab2 < 0)
			$tab2 = -$tab2;
		while ($tab1 % $tab2 != 1)
		{
			if ($tab1 % $i == 0)
			{
				if ($tab2 % $i == 0)
				{
					$tab1 = $tab1 / $i;
					$tab2 = $tab2 / $i;
					$i = 0;
				}
			}
			$i++;
		}
		if ($sol < 0)
			print ("the solution is :\n"."-".$tab1."/".$tab2."\n");
		else
			print ("the solution is :\n".$tab1."/".$tab2."\n");
	}

	function verif_decimal($sol)
	{
		$sol = (string)$sol;
		$num = '';
		$dec = false;
		$ix = strlen( $sol );

		for( $i = 0; $i < $ix; $i++ )
		{
			if( $sol{$i} == '.' )
				$dec = true;
			else
				$num = $num.$sol{$i};
		}
		return ($dec);
	}

	function verif_negatif($tab)
	{
		$i = 0;
		while ($tab[$i])
		{
			if ($tab[$i][0][0] == "-")
				$tab[$i][1] = -($tab[$i][1]);
			$i++;
		}
		return ($tab);
	}

	function verif_puissance($tab1, $tab2)
	{
		$i = 0;
		while ($tab1[$i])
		{
			if ($tab1[$i][2] != 0 && $tab1[$i][2] != 1 && $tab1[$i][2] != 2)
				return (1);
			$i++;
		}
		$i = 0;
		while ($tab2[$i])
		{
			if ($tab2[$i][2] != 0 && $tab2[$i][2] != 1 && $tab2[$i][2] != 2)
				return (1);
			$i++;
		}
		return (0);
	}

	function test_egalite($tab1, $tab2)
	{
		$i = 0;
		$res1 = 0;
		$res2 = 0;
		$op = 0;
		while ($tab1[$i])
		{
			$res1 = $res1 + $tab1[$i][1];
			$i++;
		}
		$i = 0;
		while ($tab2[$i])
		{
			$res2 = $res2 + $tab2[$i][1];
			$i++;
		}
		$op = ($res1 - $res2);
		if ($res1 == $res2){
			print("Tous les entiers sont solution !\n");
			return (1);
		}
		else{
			print("Il n'y a pas de solutons\n");
			return (1);
		}
		return (0);
	}

	function reduction($tab, $deg)
	{
		$puissance = 0;
		while ($puissance != $deg + 1)
		{
			$i = 0;
			$drap = -1;
			while ($tab[$i])
			{
				if ($drap == -1 && $tab[$i][2] == $puissance)
					$drap = $i;
				else if ($tab[$i][2] == $puissance)
				{
					//if ($tab[$i][0][0] == $tab[$drap][0][0])
						$tab[$drap][1] = $tab[$drap][1] + $tab[$i][1];
					//else
					$tab[$i][2] = "null";
				}
				$i++;
			}
			$puissance++;
		}

		$i = 0;
		while ($tab[$i])
		{
			if ($tab[$i][2] == "null" && $tab[$i + 1] != NULL || $tab[$i][1] == 0)
			{
				if ($tab[$i + 1] != null)
				{
					$tab[$i] = $tab[$i + 1];
					$tab[$i + 1][2] = "null";
				}
			}
			$i++;
		}
		$i = 0;
		while ($tab[$i])
		{
			if ($tab[$i][2] == "null")
				unset($tab[$i]);
			$i++;
		}
		$i = 0;
		if ($tab[0] == null && $tab[1])
		{
			$tab[0] = $tab[1];
			unset($tab[1]);
		}

		$temp = 0;
		$drap = 1;
		while ($drap == 1)
		{
			$drap = 0;
			$i = 0;
			while ($tab[$i])
			{
				if ($tab[$i][2] > $tab[$i + 1][2] && is_numeric($tab[$i + 1][2]))
				{
					$temp = $tab[$i];
					$tab[$i] = $tab[$i + 1];
					$tab[$i + 1] = $temp;
					$drap = 1;
				}
				$i++;
			}
		}
		return ($tab);
	}

	function concatene($tab1, $tab2)
	{
		$i = 0;
		$y = 0;
		$istock = 0;
		while ($tab1[$i])
			$i++;
		$istock = $i;
		while ($tab2[$y])
		{
			$tab1[$i] = $tab2[$y];
			if ($tab1[$i][0][0] == '+' && ($tab1[$i][0][2] == '-' || $tab1[$i][0][2] == '+'))
			{
				if ($tab1[$i][0][2] == '-')
					$tab1[$i][0][2] = '+';
				else
					$tab1[$i][0][2] = '-';
				$tab1[$i][1] = - ($tab1[$i][1]);
			}
			else
			{
				if ($tab1[$i][0][0] == '-')
					$tab1[$i][0][0] = '+';
				else
					$tab1[$i][0][0] = '-';
				$tab1[$i][1] = - ($tab1[$i][1]);
			}
			$tab2[$y] = NULL;
			$i++;
			$y++;
		}
		return ($tab1);
	}

	function print_equation($tab)
	{
		$i = 0;
		$ok;
		while ($tab[$i])
		{
			if ($tab[$i][1] > 0)
				$ok = '+';
			else
				$ok = '-';
			if ($i == 0)
			{
					print($tab[$i][1]." * X^".$tab[$i][2]);
					$tab[$i][0] = $tab[$i][1]." * X^".$tab[$i][2];
			}
			else
			{
				if ($tab[$i][1] > 0)
				{
					print(" + ".$tab[$i][1]." * X^".$tab[$i][2]);
					$tab[$i][0] = "+ ".$ok." ".$tab[$i][1]." * X^".$tab[$i][2];
				}
				if ($tab[$i][1] < 0)
				{
					print(" - ".-($tab[$i][1])." * X^".$tab[$i][2]);
					$tab[$i][0] = "- ".$ok." ".-($tab[$i][1])." * X^".$tab[$i][2];
				}
				/*if ($tab[$i][0][0] == $ok)
				{
					print(" ".$ok." ".$tab[$i][1]." * X^".$tab[$i][2]);
					$tab[$i][0] = $ok." ".$tab[$i][1]." * X^".$tab[$i][2];
				}
				else
				{
					print(" - ".$tab[$i][1]." * X^".$tab[$i][2]);
					$tab[$i][0] = "- ".$ok." ".$tab[$i][1]." * X^".$tab[$i][2];
				}*/
			}
			$i++;
		}
		print(" = 0\n");
		return ($tab);
	}
	function addxo($tab)
	{
		if ($tab[0][2] == 1)
		{
			$tab[0][1] = 0;
			$tab[0][2] = 0;
			$tab[0][0] = "0 * X^0";
			$tab[2] = $tab[1];
			$tab[1] = $tab[0];
		}
		else if ($tab[0][2] == 2)
		{
			$tab[2] = $tab[0];
			$tab[0][1] = 0;
			$tab[0][2] = 0;
			$tab[0][0] = "0 * X^0";
			$tab[1][1] = 0;
			$tab[1][2] = 1;
			$tab[1][0] = "0 * X^1";
		}
		else
		{
			$tab[2] = $tab[1];
			$tab[1][1] = 0;
			$tab[1][2] = 1;
			$tab[1][0] = "0 * X^1";
		}
		return ($tab);
	}

	function solution_degres_2($tab)
	{
		$i = 0;
		while ($tab[$i])
		{
			if ($tab[$i][0][0] == '-' && $tab[$i][1] > 0)
				$tab[$i][1] = $tab[$i][1] * -1;
			$i++;
		}
		if ($tab[2][1] == 0)
		{
			print("Division par 0 impossible, arret du programme ( 0 * X^2 )\n");
			exit (0);
		}
		$i = 0;
		$delta = $tab[1][1] * $tab[1][1] - 4 * $tab[2][1] * $tab[0][1];
		if ($delta < 0)
		{
			$delta = -$delta;
			print("Discriminant is strictly negative, no solution\n");
			echo "(".(-$tab[1][1])." - i racine ".($delta)."/".(2 * $tab[2][1]).")"."\n";
			echo "(".(-$tab[1][1])." + i racine ".($delta)."/".(2 * $tab[2][1]).")"."\n";
		}
		else if ($delta == 0)
		{
			print("Discriminant is strictly equal, unique solution :\n");
			$delta = ($tab[1][1] * -1) / (2 * $tab[2][1]);
			print ($delta."\n");
		}
		else
		{
			print("Discriminant is strictly positive, the two solutions are :\n");
			$delta = sqrt($delta);
			$a1 = ($tab[1][1] * -1 - $delta) / (2 * $tab[2][1]);
			$a2 = ($tab[1][1] * -1 + $delta) / (2 * $tab[2][1]);
			print($a1."\n");
			print($a2."\n");
		}
		exit(0);
	}

	function solution_degres_1($tab)
	{
		if ($tab[0][2] == 1)
			print("the solution is :\n0\n");
		else
		{
			$sol = $tab[0][1] / (-$tab[1][1]);
			if (verif_decimal($sol) == true)
				reduction_fraction($tab[0][1], $tab[1][1], $sol);
			else
				print ("the solution is :\n".$sol."\n");
		}
		exit (0);
	}
?>
