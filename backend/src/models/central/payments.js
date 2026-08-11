const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('payments', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    store_type: {
      type: DataTypes.INTEGER,
      allowNull: true,
      defaultValue: 1
    },
    inwarddate: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    bill_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    receipt_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    bill_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    total_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: true
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    pay_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    goods_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "Y"
    }
  }, {
    sequelize,
    tableName: 'payments',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
