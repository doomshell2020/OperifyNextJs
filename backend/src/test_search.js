async function testSearch() {
  try {
    console.log('Logging in...');
    const loginResponse = await fetch('http://localhost:5000/api/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ mobile: '9772977766', password: '12345' })
    });
    const loginData = await loginResponse.json();
    const token = loginData.data.accessToken;

    console.log('\nTesting search with contract_id = 89:');
    const res1 = await fetch('http://localhost:5000/api/contracts?contract_id=89', {
      headers: { Authorization: `Bearer ${token}` }
    });
    const data1 = await res1.json();
    console.log('Response Status:', res1.status);
    console.log('Results Count:', data1.data?.length);
    console.log('Results:', JSON.stringify(data1.data, null, 2));

    console.log('\nTesting search with contract_id = #89:');
    const res2 = await fetch('http://localhost:5000/api/contracts?contract_id=%2389', {
      headers: { Authorization: `Bearer ${token}` }
    });
    const data2 = await res2.json();
    console.log('Response Status:', res2.status);
    console.log('Results Count:', data2.data?.length);
    console.log('Results:', JSON.stringify(data2.data, null, 2));
    
    process.exit(0);
  } catch (error) {
    console.error('Test failed:', error.message);
    process.exit(1);
  }
}
testSearch();
