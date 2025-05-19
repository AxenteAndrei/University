function f = casteljau(b)

t=0:0.01:1;
B0=(1-t).^3;
B1=3*(1-t).^2.*t;
B2=3*(1-t).*(t.^2);
B3=t.^3;

B=[B0;B1;B2;B3];

f=b*B; 

plot(b(1,:),b(2,:),'r-')
plot(f(1,:),f(2,:),'b')
axis([-6 6 -4.5 4.5]);
hold on;

t=1/2;
b1=zeros(2,3); b2=zeros(2,2);
b3=zeros(2,1);
for i=1:3
b1(:,i)=b(:,i)*(1-t)+b(:,i+1)*t;
end

for i=1:2
b2(:,i)=b1(:,i)*(1-t)+b1(:,i+1)*t;
end

b3(:,1)=b2(:,1)*(1-t)+b2(:,2)*t;
plot(b1(1,:),b1(2,:),'g*')

plot(b1(1,:),b1(2,:),'g-')
plot(b2(1,:),b2(2,:),'k*')
plot(b2(1,:),b2(2,:),'k-')
plot(b3(1,:),b3(2,:),'m*')

end