const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('grn_inspection', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    po_id: {
      type: DataTypes.STRING(55),
      allowNull: false
    },
    inspection_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    vendor_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    inwarddate: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    bill_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    bill_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    total_qty: {
      type: DataTypes.FLOAT,
      allowNull: false
    },
    total_tax: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    total_amt: {
      type: DataTypes.DOUBLE(10,2),
      allowNull: false
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "Y"
    }
  }, {
    sequelize,
    tableName: 'grn_inspection',
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
