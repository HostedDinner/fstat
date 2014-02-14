<?php
/**
 * Representing a country object
 * parameters for construct are the filenames of the csv file(s)
 *
 * @author Fabian
 */
class Country {
    private $filenames;
    private $country;
    private $countryShort;
    
    public function __construct() {
        $this->country = "Unknown";
        $this->countryShort = "fam.png";
        $this->filenames = func_get_args();
    }
    
    
    public function parse($lookupip){
        $lookupip_long = sprintf("%u", ip2long($lookupip));
        
        foreach ($this->filenames as $filename){
            $isFound = $this->find($filename, $lookupip_long);
            if($isFound){
                //found IP, must not search in further files
                break;
            }
        }
    }
    
    
    private function find($filename, $lookupip_long){
        if(is_readable($filename)){
            $handle = fopen($filename,"r");
            
            //Seek to the end
            fseek($handle, 0, SEEK_END);
            $high = ftell($handle);
            $low = 0;

            while ($low <= $high) {
                $mid = floor(($low + $high) / 2);

                fseek($handle, $mid);

                if ($mid != 0) {
                    //Read a line to move to eol
                    fgets($handle);
                }

                //Read a line to get data
                $data = fgetcsv($handle, 200, ",");

                if (($data[0] <= $lookupip_long) and ($lookupip_long <= $data[1])) {
                    $this->countryShort = strtolower($data[2]) . ".png";
                    $this->country = ucwords(strtolower($data[4]));
                    fclose($handle);
                    //break here, we have found our entry
                    return true; 
                } else {
                    if ($lookupip_long < $data[0]) {
                        $high = $mid - 1;
                    } else {
                        $low = $mid + 1;
                    }
                }
            }

            fclose($handle);
            return false;
        }else{
            return false;
        }
    }
    
    
    //Basic Getter
    public function getCountry(){
        return $this->country;
    }
    
    public function getCountryShort(){
        return $this->countryShort;
    }
    
    
    public static $countryOffset = array(
        'ad.png' => -0,
        'ae.png' => -11,
        'af.png' => -22,
        'ag.png' => -33,
        'ai.png' => -44,
        'al.png' => -55,
        'am.png' => -66,
        'an.png' => -77,
        'ao.png' => -88,
        'ar.png' => -99,
        'as.png' => -110,
        'at.png' => -121,
        'au.png' => -132,
        'aw.png' => -143,
        'ax.png' => -154,
        'az.png' => -165,
        'ba.png' => -176,
        'bb.png' => -187,
        'bd.png' => -198,
        'be.png' => -209,
        'bf.png' => -220,
        'bg.png' => -231,
        'bh.png' => -242,
        'bi.png' => -253,
        'bj.png' => -264,
        'bm.png' => -275,
        'bn.png' => -286,
        'bo.png' => -297,
        'br.png' => -308,
        'bs.png' => -319,
        'bt.png' => -330,
        'bv.png' => -341,
        'bw.png' => -352,
        'by.png' => -363,
        'bz.png' => -374,
        'ca.png' => -385,
        'catalonia.png' => -396,
        'cc.png' => -407,
        'cd.png' => -418,
        'cf.png' => -429,
        'cg.png' => -440,
        'ch.png' => -451,
        'ci.png' => -462,
        'ck.png' => -473,
        'cl.png' => -484,
        'cm.png' => -495,
        'cn.png' => -506,
        'co.png' => -517,
        'cr.png' => -528,
        'cs.png' => -539,
        'cu.png' => -550,
        'cv.png' => -561,
        'cx.png' => -572,
        'cy.png' => -583,
        'cz.png' => -594,
        'de.png' => -605,
        'dj.png' => -616,
        'dk.png' => -627,
        'dm.png' => -638,
        'do.png' => -649,
        'dz.png' => -660,
        'ec.png' => -671,
        'ee.png' => -682,
        'eg.png' => -693,
        'eh.png' => -704,
        'england.png' => -715,
        'er.png' => -726,
        'es.png' => -737,
        'et.png' => -748,
        'europeanunion.png' => -759,
        'fam.png' => -770,
        'fi.png' => -781,
        'fj.png' => -792,
        'fk.png' => -803,
        'fm.png' => -814,
        'fo.png' => -825,
        'fr.png' => -836,
        'ga.png' => -847,
        'gb.png' => -858,
        'gd.png' => -869,
        'ge.png' => -880,
        'gf.png' => -891,
        'gh.png' => -902,
        'gi.png' => -913,
        'gl.png' => -924,
        'gm.png' => -935,
        'gn.png' => -946,
        'gp.png' => -957,
        'gq.png' => -968,
        'gr.png' => -979,
        'gs.png' => -990,
        'gt.png' => -1001,
        'gu.png' => -1012,
        'gw.png' => -1023,
        'gy.png' => -1034,
        'hk.png' => -1045,
        'hm.png' => -1056,
        'hn.png' => -1067,
        'hr.png' => -1078,
        'ht.png' => -1089,
        'hu.png' => -1100,
        'id.png' => -1111,
        'ie.png' => -1122,
        'il.png' => -1133,
        'in.png' => -1144,
        'io.png' => -1155,
        'iq.png' => -1166,
        'ir.png' => -1177,
        'is.png' => -1188,
        'it.png' => -1199,
        'jm.png' => -1210,
        'jo.png' => -1221,
        'jp.png' => -1232,
        'ke.png' => -1243,
        'kg.png' => -1254,
        'kh.png' => -1265,
        'ki.png' => -1276,
        'km.png' => -1287,
        'kn.png' => -1298,
        'kp.png' => -1309,
        'kr.png' => -1320,
        'kw.png' => -1331,
        'ky.png' => -1342,
        'kz.png' => -1353,
        'la.png' => -1364,
        'lb.png' => -1375,
        'lc.png' => -1386,
        'li.png' => -1397,
        'lk.png' => -1408,
        'lr.png' => -1419,
        'ls.png' => -1430,
        'lt.png' => -1441,
        'lu.png' => -1452,
        'lv.png' => -1463,
        'ly.png' => -1474,
        'ma.png' => -1485,
        'mc.png' => -1496,
        'md.png' => -1507,
        'me.png' => -1518,
        'mg.png' => -1529,
        'mh.png' => -1540,
        'mk.png' => -1551,
        'ml.png' => -1562,
        'mm.png' => -1573,
        'mn.png' => -1584,
        'mo.png' => -1595,
        'mp.png' => -1606,
        'mq.png' => -1617,
        'mr.png' => -1628,
        'ms.png' => -1639,
        'mt.png' => -1650,
        'mu.png' => -1661,
        'mv.png' => -1672,
        'mw.png' => -1683,
        'mx.png' => -1694,
        'my.png' => -1705,
        'mz.png' => -1716,
        'na.png' => -1727,
        'nc.png' => -1738,
        'ne.png' => -1749,
        'nf.png' => -1760,
        'ng.png' => -1771,
        'ni.png' => -1782,
        'nl.png' => -1793,
        'no.png' => -1804,
        'np.png' => -1815,
        'nr.png' => -1826,
        'nu.png' => -1837,
        'nz.png' => -1848,
        'om.png' => -1859,
        'pa.png' => -1870,
        'pe.png' => -1881,
        'pf.png' => -1892,
        'pg.png' => -1903,
        'ph.png' => -1914,
        'pk.png' => -1925,
        'pl.png' => -1936,
        'pm.png' => -1947,
        'pn.png' => -1958,
        'pr.png' => -1969,
        'ps.png' => -1980,
        'pt.png' => -1991,
        'pw.png' => -2002,
        'py.png' => -2013,
        'qa.png' => -2024,
        're.png' => -2035,
        'ro.png' => -2046,
        'rs.png' => -2057,
        'ru.png' => -2068,
        'rw.png' => -2079,
        'sa.png' => -2090,
        'sb.png' => -2101,
        'sc.png' => -2112,
        'scotland.png' => -2123,
        'sd.png' => -2134,
        'se.png' => -2145,
        'sg.png' => -2156,
        'sh.png' => -2167,
        'si.png' => -2178,
        'sj.png' => -2189,
        'sk.png' => -2200,
        'sl.png' => -2211,
        'sm.png' => -2222,
        'sn.png' => -2233,
        'so.png' => -2244,
        'sr.png' => -2255,
        'st.png' => -2266,
        'sv.png' => -2277,
        'sy.png' => -2288,
        'sz.png' => -2299,
        'tc.png' => -2310,
        'td.png' => -2321,
        'tf.png' => -2332,
        'tg.png' => -2343,
        'th.png' => -2354,
        'tj.png' => -2365,
        'tk.png' => -2376,
        'tl.png' => -2387,
        'tm.png' => -2398,
        'tn.png' => -2409,
        'to.png' => -2420,
        'tr.png' => -2431,
        'tt.png' => -2442,
        'tv.png' => -2453,
        'tw.png' => -2464,
        'tz.png' => -2475,
        'ua.png' => -2486,
        'ug.png' => -2497,
        'um.png' => -2508,
        'us.png' => -2519,
        'uy.png' => -2530,
        'uz.png' => -2541,
        'va.png' => -2552,
        'vc.png' => -2563,
        've.png' => -2574,
        'vg.png' => -2585,
        'vi.png' => -2596,
        'vn.png' => -2607,
        'vu.png' => -2618,
        'wales.png' => -2629,
        'wf.png' => -2640,
        'ws.png' => -2651,
        'ye.png' => -2662,
        'yt.png' => -2673,
        'za.png' => -2684,
        'zm.png' => -2695,
        'zw.png' => -2706
    );

}
?>